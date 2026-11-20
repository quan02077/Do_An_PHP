<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('admin.checkins', fn ($user) => $user->vai_tro === 'admin');
