<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class SmtpSetting extends Model
{
    protected $fillable = [
        'mail_mailer',
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_encryption',
        'mail_from_address',
        'mail_from_name',
        'is_active',
    ];

    protected $hidden = [
        'mail_password',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'mail_port' => 'integer',
        ];
    }

    public function setMailPasswordAttribute(?string $value): void
    {
        $this->attributes['mail_password'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getMailPasswordDecryptedAttribute(): ?string
    {
        if (!$this->attributes['mail_password']) {
            return null;
        }
        try {
            return Crypt::decryptString($this->attributes['mail_password']);
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function getActive(): ?self
    {
        return self::where('is_active', true)->first();
    }
}
