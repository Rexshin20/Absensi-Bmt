<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\OfficeLocation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_attendance_row_opens_a_detail_view_instead_of_showing_a_round_photo(): void
    {
        $admin = User::create([
            'name' => 'Admin BMT',
            'email' => 'admin@example.test',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);
        $employee = Employee::create([
            'name' => 'Karyawan Detail',
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
            'photo_path' => 'absensi/1/foto.jpg',
            'source' => 'web',
        ]);

        $this->actingAs($admin)
            ->get(route('attendances.index'))
            ->assertOk()
            ->assertSee('data-attendance-detail', false)
            ->assertSee('data-employee="Karyawan Detail"', false)
            ->assertSee('attendance-detail-modal', false)
            ->assertSee('attendance-detail-photo', false)
            ->assertSee('Lihat')
            ->assertDontSee('attendance-photo', false);
    }

    public function test_attendance_page_can_filter_records_by_office_location(): void
    {
        $admin = User::create([
            'name' => 'Admin BMT',
            'email' => 'admin@example.test',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);
        $officeA = OfficeLocation::create([
            'name' => 'Kantor Pusat',
            'code' => 'PUSAT',
            'address' => 'Alamat kantor pusat',
            'latitude' => -6.2,
            'longitude' => 106.8,
        ]);
        $officeB = OfficeLocation::create([
            'name' => 'Kantor Cabang',
            'code' => 'CABANG',
            'address' => 'Alamat kantor cabang',
            'latitude' => -6.3,
            'longitude' => 106.9,
        ]);
        $employeeA = Employee::create([
            'name' => 'Karyawan Pusat',
            'office_location_id' => $officeA->id,
            'is_active' => true,
        ]);
        $employeeB = Employee::create([
            'name' => 'Karyawan Cabang',
            'office_location_id' => $officeB->id,
            'is_active' => true,
        ]);

        foreach ([[$employeeA, $officeA], [$employeeB, $officeB]] as [$employee, $office]) {
            Attendance::create([
                'employee_id' => $employee->id,
                'office_location_id' => $office->id,
                'date' => now()->toDateString(),
                'check_in_time' => '08:00:00',
                'status' => 'present',
                'latitude' => -6.2,
                'longitude' => 106.8,
                'distance_m' => 10,
                'source' => 'web',
            ]);
        }

        $this->actingAs($admin)
            ->get(route('attendances.index', ['location_id' => $officeA->id]))
            ->assertOk()
            ->assertSee('Karyawan Pusat')
            ->assertDontSee('Karyawan Cabang')
            ->assertSee('Semua lokasi kantor')
            ->assertSee('Kantor Pusat');
    }
}
