@extends('layouts.app')

@section('title', 'Hari Kerja')

@push('styles')
    <style>
        .day-options { display: flex; flex-wrap: wrap; gap: 0.75rem 1.5rem; }
        .day-options .checkbox-label { font-size: 0.9375rem; color: var(--text-primary); }
    </style>
@endpush

@section('content')
    <div class="grid">
        <div class="grid-item col-12">
            <div class="d-flex align-center justify-between flex-wrap gap-3">
                <div>
                    <h5 class="mb-1">Hari Kerja</h5>
                    <p class="text-body2 mb-0">Pakai untuk menentukan status absen dan libur mingguan kantor.</p>
                </div>
                <span class="chip chip-sm">Jam masuk {{ $workStart }} WIB</span>
            </div>
        </div>

        @if (session('status'))
            <div class="grid-item col-12">
                <div class="alert alert-success">
                    <span class="alert-icon"><x-icon name="CheckCircleOutline" /></span>
                    <div class="alert-message">{{ session('status') }}</div>
                </div>
            </div>
        @endif

        <div class="grid-item col-12 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-header-title" style="font-size: 1.25rem;">Pengaturan Hari Kerja</div>
                        <p class="card-header-subheader">Absen setelah jam masuk otomatis berstatus Terlambat.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('work-settings.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="card-content">
                        @if ($errors->any())
                            <div class="alert alert-error mb-5">
                                <span class="alert-icon"><x-icon name="AlertCircleOutline" /></span>
                                <div class="alert-message">{{ $errors->first() }}</div>
                            </div>
                        @endif

                        <div class="grid">
                            <div class="grid-item col-12 col-md-4">
                                <div class="form-control">
                                    <input id="work_start_time" name="work_start_time" type="time"
                                           value="{{ old('work_start_time', $workStart) }}"
                                           class="input @error('work_start_time') input-error @enderror">
                                    <label class="form-label-float" for="work_start_time">Jam masuk</label>
                                </div>
                            </div>

                            <div class="grid-item col-12 col-md-8">
                                <span class="form-label">Hari kerja</span>
                                <div class="day-options">
                                    @foreach ($days as $value => $label)
                                        <label class="checkbox-label">
                                            <input type="checkbox" name="work_days[]" value="{{ $value }}"
                                                   @checked(in_array($value, $workDays, true))>
                                            {{ $label }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <p class="form-helper" style="margin-left: 0;">
                            Hari yang tidak dicentang otomatis dianggap libur mingguan, contoh: centang Senin sampai Sabtu jika kantor libur setiap Minggu.
                        </p>
                    </div>

                    <div class="card-actions justify-end">
                        <button type="submit" class="btn btn-contained">Simpan Pengaturan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
