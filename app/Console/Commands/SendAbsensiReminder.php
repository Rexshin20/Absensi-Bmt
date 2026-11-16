<?php

namespace App\Console\Commands;

use App\Models\DeviceToken;
use App\Models\Holiday;
use App\Services\AttendanceService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class SendAbsensiReminder extends Command
{
    protected $signature = 'absensi:reminder';

    protected $description = 'Kirim pengingat absen lewat Firebase ke karyawan yang belum absen.';

    public function handle(): int
    {
        $now = now();
        $today = $now->toDateString();

        if (! in_array((int) $now->isoWeekday(), AttendanceService::workDays(), true)) {
            return self::SUCCESS;
        }

        $isHoliday = Holiday::where('start_date', '<=', $today)
            ->where(function ($query) use ($today) {
                $query->whereNull('end_date')->orWhere('end_date', '>=', $today);
            })
            ->exists();

        if ($isHoliday) {
            return self::SUCCESS;
        }

        $credentialsPath = config('services.firebase.credentials');

        if (! is_string($credentialsPath) || ! is_file($credentialsPath)) {
            $this->error('Kredensial Firebase belum ada: ' . $credentialsPath);

            return self::FAILURE;
        }

        $credentials = json_decode((string) file_get_contents($credentialsPath), true);

        if (! is_array($credentials)
            || empty($credentials['project_id'])
            || empty($credentials['client_email'])
            || empty($credentials['private_key'])) {
            $this->error('Format kredensial Firebase tidak valid.');

            return self::FAILURE;
        }

        $tokens = DeviceToken::query()
            ->whereHas('user.employee', function ($query) use ($today) {
                $query->where('is_active', true)
                    ->whereDoesntHave('attendances', fn ($attendance) => $attendance->where('date', $today))
                    ->whereDoesntHave('leaveRequests', fn ($leave) => $leave
                        ->where('status', 'approved')
                        ->where('start_date', '<=', $today)
                        ->where('end_date', '>=', $today));
            })
            ->get();

        if ($tokens->isEmpty()) {
            $this->info('Tidak ada perangkat yang perlu dikirimi pengingat.');

            return self::SUCCESS;
        }

        try {
            $accessToken = $this->accessToken($credentials);
        } catch (RuntimeException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $sent = 0;
        $failed = 0;

        foreach ($tokens as $deviceToken) {
            $response = Http::withToken($accessToken)->post(
                "https://fcm.googleapis.com/v1/projects/{$credentials['project_id']}/messages:send",
                [
                    'message' => [
                        'token' => $deviceToken->token,
                        'notification' => [
                            'title' => 'Waktunya absen',
                            'body' => 'Kamu belum absen hari ini. Jangan lupa foto selfie dan aktifkan lokasi.',
                        ],
                        'android' => ['priority' => 'high'],
                    ],
                ]
            );

            if ($response->successful()) {
                $sent++;
                continue;
            }

            $failed++;
            $errorCode = $response->json('error.details.0.errorCode');

            if (in_array($errorCode, ['UNREGISTERED', 'INVALID_ARGUMENT'], true)) {
                $deviceToken->delete();
            }
        }

        $this->info("Pengingat terkirim: {$sent}, gagal: {$failed}.");

        return self::SUCCESS;
    }

    private function accessToken(array $credentials): string
    {
        $cacheKey = 'firebase_access_token_' . sha1((string) $credentials['client_email']);
        $cached = Cache::get($cacheKey);

        if (is_string($cached) && $cached !== '') {
            return $cached;
        }

        $issuedAt = time();
        $header = $this->base64Url((string) json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $claims = $this->base64Url((string) json_encode([
            'iss' => $credentials['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $issuedAt,
            'exp' => $issuedAt + 3600,
        ]));

        $signature = '';
        $signed = openssl_sign($header . '.' . $claims, $signature, $credentials['private_key'], 'sha256');

        if (! $signed) {
            throw new RuntimeException('Gagal menandatangani permintaan Firebase.');
        }

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $header . '.' . $claims . '.' . $this->base64Url($signature),
        ]);

        $token = $response->json('access_token');

        if (! is_string($token) || $token === '') {
            throw new RuntimeException('Gagal mengambil token Google: ' . $response->body());
        }

        Cache::put($cacheKey, $token, now()->addMinutes(50));

        return $token;
    }

    private function base64Url(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
