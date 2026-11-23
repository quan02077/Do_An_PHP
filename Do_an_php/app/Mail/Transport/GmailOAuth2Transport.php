<?php

namespace App\Mail\Transport;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mailer\Transport\Smtp\Auth\XOAuth2Authenticator;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;

class GmailOAuth2Transport extends AbstractTransport
{
    public function __construct(
        protected string $clientId,
        protected string $clientSecret,
        protected string $refreshToken,
        protected string $username,
        protected string $mode = 'smtp',
        protected string $host = 'smtp.gmail.com',
        protected int $port = 587,
        protected int $timeout = 15,
    ) {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        $wasCached = Cache::has($this->cacheKey());
        $accessToken = $this->fetchAccessToken();

        if ($this->mode === 'api') {
            $this->sendViaApi($message, $accessToken, $wasCached);

            return;
        }

        try {
            $this->sendViaSmtp($message, $accessToken);
        } catch (TransportException $e) {
            if (!$wasCached) {
                throw $e;
            }

            Cache::forget($this->cacheKey());
            $freshToken = $this->fetchAccessToken(true);
            $this->sendViaSmtp($message, $freshToken);
        }
    }

    protected function sendViaSmtp(SentMessage $message, string $accessToken): void
    {
        $useImplicitTls = $this->port === 465 ? true : null;

        $smtp = new EsmtpTransport(
            $this->host,
            $this->port,
            $useImplicitTls,
            null,
            null,
            null,
            [new XOAuth2Authenticator()],
        );

        $smtp->setUsername($this->username);
        $smtp->setPassword($accessToken);

        $stream = $smtp->getStream();
        if (method_exists($stream, 'setTimeout')) {
            $stream->setTimeout($this->timeout);
        }

        $smtp->send($message->getOriginalMessage(), $message->getEnvelope());
    }

    protected function sendViaApi(SentMessage $message, string $accessToken, bool $allowRetry = false): void
    {
        $raw = rtrim(strtr(base64_encode($message->toString()), '+/', '-_'), '=');

        $response = Http::withToken($accessToken)
            ->timeout($this->timeout)
            ->post('https://gmail.googleapis.com/gmail/v1/users/me/messages/send', [
                'raw' => $raw,
            ]);

        if ($response->status() === 401 && $allowRetry) {
            Cache::forget($this->cacheKey());
            $freshToken = $this->fetchAccessToken(true);
            $this->sendViaApi($message, $freshToken, false);

            return;
        }

        if (!$response->successful()) {
            $error = $response->json('error.message') ?? $response->body();

            throw new TransportException('Gửi email qua Gmail API thất bại: ' . $error);
        }
    }

    protected function fetchAccessToken(bool $forceRefresh = false): string
    {
        $this->ensureConfigured();

        $cacheKey = $this->cacheKey();
        if (!$forceRefresh) {
            $cached = Cache::get($cacheKey);
            if (is_string($cached) && $cached !== '') {
                return $cached;
            }
        }

        $response = Http::asForm()
            ->timeout($this->timeout)
            ->post('https://oauth2.googleapis.com/token', [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'refresh_token' => $this->refreshToken,
                'grant_type' => 'refresh_token',
            ]);

        if (!$response->successful()) {
            $error = $response->json('error_description')
                ?? $response->json('error')
                ?? $response->body();

            throw new TransportException('Không thể lấy Gmail OAuth2 access token: ' . $error);
        }

        $accessToken = (string) $response->json('access_token', '');
        if ($accessToken === '') {
            throw new TransportException('Google OAuth2 không trả về access_token hợp lệ.');
        }

        $expiresIn = (int) $response->json('expires_in', 3600);
        Cache::put($cacheKey, $accessToken, now()->addSeconds(max(60, $expiresIn - 60)));

        return $accessToken;
    }

    protected function ensureConfigured(): void
    {
        if (
            $this->clientId === ''
            || $this->clientSecret === ''
            || $this->refreshToken === ''
            || $this->username === ''
        ) {
            throw new TransportException(
                'Thiếu cấu hình Gmail OAuth2 (GMAIL_OAUTH_CLIENT_ID, GMAIL_OAUTH_CLIENT_SECRET, GMAIL_OAUTH_REFRESH_TOKEN, MAIL_USERNAME).'
            );
        }
    }

    protected function cacheKey(): string
    {
        return 'gmail_oauth2_access_token_' . sha1($this->clientId . '|' . $this->username);
    }

    public function __toString(): string
    {
        return sprintf('gmail+oauth2+%s://%s', $this->mode, $this->host);
    }
}
