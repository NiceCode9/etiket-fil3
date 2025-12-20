<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Midtrans Server Key
    |--------------------------------------------------------------------------
    |
    | Server key dari Midtrans dashboard. Digunakan untuk autentikasi
    | request ke Midtrans API dari backend.
    |
    */
    'server_key' => env('MIDTRANS_SERVER_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Midtrans Client Key
    |--------------------------------------------------------------------------
    |
    | Client key dari Midtrans dashboard. Digunakan untuk inisialisasi
    | Snap popup di frontend/browser.
    |
    */
    'client_key' => env('MIDTRANS_CLIENT_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Production Mode
    |--------------------------------------------------------------------------
    |
    | Set true untuk production mode, false untuk sandbox/testing.
    | Sandbox: https://dashboard.sandbox.midtrans.com
    | Production: https://dashboard.midtrans.com
    |
    */
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),

    /*
    |--------------------------------------------------------------------------
    | Sanitized Mode
    |--------------------------------------------------------------------------
    |
    | Set true untuk sanitize data sebelum dikirim ke Midtrans.
    | Recommended: true
    |
    */
    'is_sanitized' => env('MIDTRANS_IS_SANITIZED', true),

    /*
    |--------------------------------------------------------------------------
    | 3DS Authentication
    |--------------------------------------------------------------------------
    |
    | Set true untuk menggunakan 3D Secure authentication untuk
    | kartu kredit. Recommended: true
    |
    */
    'is_3ds' => env('MIDTRANS_IS_3DS', true),

    /*
    |--------------------------------------------------------------------------
    | Payment Expiry Duration
    |--------------------------------------------------------------------------
    |
    | Durasi expiry untuk pembayaran dalam jam.
    | Default: 24 jam
    |
    */
    'payment_expiry_hours' => env('MIDTRANS_PAYMENT_EXPIRY_HOURS', 24),

    /*
    |--------------------------------------------------------------------------
    | Notification URL
    |--------------------------------------------------------------------------
    |
    | URL untuk menerima webhook notification dari Midtrans.
    | Set ini di Midtrans dashboard juga.
    | Format: https://yourdomain.com/api/midtrans/callback
    |
    */
    'notification_url' => env('MIDTRANS_NOTIFICATION_URL', env('APP_URL') . '/api/midtrans/callback'),

    /*
    |--------------------------------------------------------------------------
    | Snap URL
    |--------------------------------------------------------------------------
    |
    | URL untuk Snap popup script. Otomatis berdasarkan mode production.
    |
    */
    'snap_url' => env('MIDTRANS_IS_PRODUCTION', false)
        ? 'https://app.midtrans.com/snap/snap.js'
        : 'https://app.sandbox.midtrans.com/snap/snap.js',

];
