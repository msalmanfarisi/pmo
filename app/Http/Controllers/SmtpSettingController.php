<?php

namespace App\Http\Controllers;

use App\Models\SmtpSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class SmtpSettingController extends Controller
{
    public function edit(): View
    {
        $setting = SmtpSetting::first();
        return view('settings.smtp', compact('setting'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mail_mailer' => ['required', 'string', 'in:smtp,sendmail,log'],
            'mail_host' => ['required', 'string', 'max:255'],
            'mail_port' => ['required', 'integer', 'min:1', 'max:65535'],
            'mail_username' => ['nullable', 'string', 'max:255'],
            'mail_password' => ['nullable', 'string', 'max:255'],
            'mail_encryption' => ['nullable', 'string', 'in:tls,ssl'],
            'mail_from_address' => ['required', 'email', 'max:255'],
            'mail_from_name' => ['required', 'string', 'max:255'],
        ]);

        $setting = SmtpSetting::first();
        if ($setting) {
            if (empty($validated['mail_password'])) {
                unset($validated['mail_password']);
            }
            $setting->update($validated);
        } else {
            SmtpSetting::create($validated);
        }

        return redirect()->route('settings.smtp')->with('success', 'SMTP settings updated successfully.');
    }

    public function test(Request $request): RedirectResponse
    {
        $request->validate([
            'test_email' => ['required', 'email', 'max:255'],
        ]);

        $setting = SmtpSetting::getActive();
        if (!$setting) {
            return back()->with('error', 'Please configure SMTP settings first.');
        }

        try {
            config([
                'mail.mailers.smtp.host' => $setting->mail_host,
                'mail.mailers.smtp.port' => $setting->mail_port,
                'mail.mailers.smtp.username' => $setting->mail_username,
                'mail.mailers.smtp.password' => $setting->mail_password_decrypted,
                'mail.mailers.smtp.encryption' => $setting->mail_encryption,
                'mail.from.address' => $setting->mail_from_address,
                'mail.from.name' => $setting->mail_from_name,
            ]);

            Mail::raw('This is a test email from PMO application.', function ($message) use ($request) {
                $message->to($request->input('test_email'))
                    ->subject('PMO - Test Email');
            });

            return back()->with('success', 'Test email sent successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send test email: ' . $e->getMessage());
        }
    }
}
