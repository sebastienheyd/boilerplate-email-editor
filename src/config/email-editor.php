<?php

return [
    'mailer' => config('mail.default'),
    'from'   => [
        'address' => config('mail.from.address'),
        'name'    => config('mail.from.name'),
    ],
    'layouts_path' => env('EMAIL_LAYOUTS_PATH', resource_path('views/email-layouts')),
];
