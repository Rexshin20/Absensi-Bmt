<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendancePhotoController extends Controller
{
    public function show(Attendance $attendance): StreamedResponse
    {
        $path = $attendance->photo_path;
        $disk = Storage::disk('local');

        abort_if(! $path || ! $disk->exists($path), 404);

        return $disk->response($path, null, [
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }
}
