<?php

namespace App\Support;

class PhoneNumber
{
    public static function normalize(mixed $phone): mixed
    {
        if (! is_string($phone) || trim($phone) === '') {
            return $phone;
        }

        $normalized = preg_replace('/[\s().\/-]+/u', '', trim($phone));

        if (! is_string($normalized)) {
            return $phone;
        }

        if (str_starts_with($normalized, '00')) {
            $normalized = '+'.substr($normalized, 2);
        } elseif (str_starts_with($normalized, '+')) {
            return $normalized;
        } elseif (str_starts_with($normalized, '358')) {
            $normalized = '+'.$normalized;
        } elseif (str_starts_with($normalized, '0')) {
            $normalized = '+358'.substr($normalized, 1);
        } else {
            $normalized = '+358'.$normalized;
        }

        return $normalized;
    }
}
