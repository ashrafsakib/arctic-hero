<?php

return [
    'name' => env('COMPANY_NAME', 'Arctic Hero'),
    'phone' => env('COMPANY_PHONE', '+358417296611'),
    'email' => env('COMPANY_EMAIL', 'ashrafulsakib740@gmail.com'),
    'website' => env('COMPANY_WEBSITE'),
    'company_number' => env('COMPANY_NUMBER', '3481963-9'),
    'vat_number' => env('COMPANY_VAT_NUMBER', 'FI34819639'),
    'service_area' => env('SERVICE_AREA', 'Oulu and nearby areas'),
    'booking_advance_days' => (int) env('BOOKING_ADVANCE_DAYS', 365),
];
