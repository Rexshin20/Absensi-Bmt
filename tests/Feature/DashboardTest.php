<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_attendance_and_hides_leave_requests(): void
    {
        $admin = User::create([
            'name' => 'Admin BMT',
            'email' => 'admin@example.test',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);
        $employee = Employee::create([
            'name' => 'Karyawan Demo',
            'is_active' => true,
        ]);
        Attendance::create([
            'employee_id' => $employee->id,
            'date' => now()->toDateString(),
            'check_in_time' => '08:00:00',
            'status' => 'present',
            'latitude' => -6.2,
            'longitude' => 106.8,
            'distance_m' => 10,
            'source' => 'web',
        ]);
        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Logo BMT Cinde Barokah')
            ->assertSeeText('Karyawan Aktif')
            ->assertSeeText('Hadir Hari Ini')
            ->assertSeeText('Karyawan Demo')
            ->assertDontSeeText('izin')
            ->assertSeeText('Sistem Informasi Absensi');
    }
}
