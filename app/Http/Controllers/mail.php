<?php
use Illuminate\Support\Facades\Mail;

Mail::raw('This is a test email.', function ($message) {
    $message->to('jopi.adrianto@gmail.com') // Replace with your test email
            ->subject('Test Email');
});
