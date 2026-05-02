<?php

namespace App\Services;

use Illuminate\Support\Str;

class CaptchaService
{
    public static function generate(): string
    {
        $chars = 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $captcha = '';
        $length = strlen($chars) - 1;
        for ($i = 0; $i < 8; $i++) {
            $captcha .= $chars[random_int(0, $length)];
        }
        return $captcha;
    }

    public static function store(string $captcha): void
    {
        session(['captcha' => $captcha, 'captcha_created_at' => now()->timestamp]);
    }

    public static function validate(string $input): bool
    {
        $stored = session('captcha');
        $createdAt = session('captcha_created_at');

        if (!$stored || !$createdAt) {
            return false;
        }

        if (now()->timestamp - $createdAt > 300) {
            self::clear();
            return false;
        }

        $valid = hash_equals($stored, $input);
        self::clear();
        return $valid;
    }

    public static function clear(): void
    {
        session()->forget(['captcha', 'captcha_created_at']);
    }
}
