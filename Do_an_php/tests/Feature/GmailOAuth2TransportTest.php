<?php

namespace Tests\Feature;

use App\Mail\Transport\GmailOAuth2Transport;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mime\Email;
use Tests\TestCase;

class GmailOAuth2TransportTest extends TestCase
{
    public function test_gmail_oauth2_mailer_resolves_custom_transport(): void
    {
        config([
            'mail.mailers.gmail_oauth2.client_id' => 'client-id',
            'mail.mailers.gmail_oauth2.client_secret' => 'client-secret',
            'mail.mailers.gmail_oauth2.refresh_token' => 'refresh-token',
            'mail.mailers.gmail_oauth2.username' => 'sender@gmail.com',
            'mail.mailers.gmail_oauth2.mode' => 'smtp',
        ]);

        $transport = Mail::mailer('gmail_oauth2')->getSymfonyTransport();

        $this->assertInstanceOf(GmailOAuth2Transport::class, $transport);
        $this->assertSame('gmail+oauth2+smtp://smtp.gmail.com', (string) $transport);
    }

    public function test_missing_oauth2_credentials_throws_transport_exception(): void
    {
        $transport = new GmailOAuth2Transport(
            clientId: '',
            clientSecret: '',
            refreshToken: '',
            username: '',
            mode: 'api',
        );

        $email = (new Email())
            ->from('sender@gmail.com')
            ->to('user@example.com')
            ->subject('Test')
            ->text('Hello');

        $this->expectException(TransportException::class);
        $transport->send($email);
    }

    public function test_api_mode_fetches_caches_token_and_sends_raw_mime_email(): void
    {
        Cache::flush();

        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response([
                'access_token' => 'fresh-access-token',
                'expires_in' => 3600,
                'token_type' => 'Bearer',
            ], 200),
            'https://gmail.googleapis.com/gmail/v1/users/me/messages/send' => Http::response([
                'id' => 'msg-123',
            ], 200),
        ]);

        $transport = new GmailOAuth2Transport(
            clientId: 'client-id',
            clientSecret: 'client-secret',
            refreshToken: 'refresh-token',
            username: 'sender@gmail.com',
            mode: 'api',
        );

        $email = (new Email())
            ->from('sender@gmail.com')
            ->to('user@example.com')
            ->subject('Đặt lại mật khẩu')
            ->text('Nội dung thử nghiệm');

        $transport->send($email);
        $transport->send($email);

        Http::assertSentCount(3);
        Http::assertSent(function (Request $request) {
            return $request->url() === 'https://oauth2.googleapis.com/token'
                && $request['grant_type'] === 'refresh_token'
                && $request['refresh_token'] === 'refresh-token';
        });
        Http::assertSent(function (Request $request) {
            return $request->url() === 'https://gmail.googleapis.com/gmail/v1/users/me/messages/send'
                && $request->hasHeader('Authorization', 'Bearer fresh-access-token')
                && is_string($request['raw'])
                && $request['raw'] !== '';
        });
    }

    public function test_api_mode_refreshes_cached_token_once_on_unauthorized_response(): void
    {
        Cache::flush();

        $transport = new GmailOAuth2Transport(
            clientId: 'client-id',
            clientSecret: 'client-secret',
            refreshToken: 'refresh-token',
            username: 'sender@gmail.com',
            mode: 'api',
        );

        Cache::put('gmail_oauth2_access_token_' . sha1('client-id|sender@gmail.com'), 'stale-token', 600);

        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response([
                'access_token' => 'refreshed-token',
                'expires_in' => 3600,
            ], 200),
            'https://gmail.googleapis.com/gmail/v1/users/me/messages/send' => Http::sequence()
                ->push(['error' => ['message' => 'Invalid Credentials']], 401)
                ->push(['id' => 'msg-456'], 200),
        ]);

        $email = (new Email())
            ->from('sender@gmail.com')
            ->to('user@example.com')
            ->subject('Retry Test')
            ->text('Body');

        $transport->send($email);

        Http::assertSent(function (Request $request) {
            return $request->url() === 'https://gmail.googleapis.com/gmail/v1/users/me/messages/send'
                && $request->hasHeader('Authorization', 'Bearer refreshed-token');
        });
    }
}
