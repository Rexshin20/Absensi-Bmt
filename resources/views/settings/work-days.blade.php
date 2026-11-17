@extends('layouts.app')

@section('title', 'Hari Kerja')

@push('styles')
    <style>
        .work-form { display: flex; flex-direction: column; gap: 1.5rem; }
        .work-time { max-width: 14rem; }
        .day-options { display: grid; grid-template-columns: repeat(auto-fit, minmax(7.5rem, 1fr)); gap: 0.5rem; }
        .day-option {
            display: flex; align-items: center; gap: 0.5rem; min-height: 2.75rem; padding: 0 0.875rem;
            border: 1px solid var(--divider); border-radius: 8px; background-color: var(--bg-paper);
            font-size: 0.875rem; color: var(--text-primary); cursor: pointer;
            transition: border-color 150ms ease, background-color 150ms ease, color 150ms ease;
        }
        .day-option:hover { border-color: var(--primary-main); }
        .day-option:has(input:checked) { border-color: var(--primary-main); background-color: rgba(8, 127, 75, 0.08); color: var(--primary-dark); font-weight: 500; }
        .day-option input { margin: 0; accent-color: var(--primary-main); }
        .work-helper { margin: 0; }
        .card-actions.work-actions { justify-content: flex-end; padding: 0.875rem 1.25rem; border-top: 1px solid var(--divider); }
    </style>
@endpush

@section('content')
    <div class="grid">
        <div class="grid-item col-12">
            <div class="d-flex align-center justify-between flex-wrap gap-3">
                <div>
                    <h5 class="mb-1">Hari Kerja</h5>
                    <p class="text-body2 mb-0">Berlaku sebagai default. Lokasi kantor bisa punya hari kerja dan jam masuk sendiri di menu Lokasi Kantor.</p>
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
                        <div class="card-header-title">Pengaturan Hari Kerja</div>
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

                        <div class="work-form">
                            <div class="work-time">
                                <div class="form-control">
                                    <input id="work_start_time" name="work_start_time" type="time"
                                           value="{{ old('work_start_time', $workStart) }}"
                                           class="input @error('work_start_time') input-error @enderror">
                                    <label class="form-label-float" for="work_start_time">Jam masuk</label>
                                </div>
                            </div>

                            <div>
                                <span class="form-label">Hari kerja</span>
                                <div class="day-options">
                                    @foreach ($days as $value => $label)
                                        <label class="day-option">
                                            <input type="checkbox" name="work_days[]" value="{{ $value }}"
                                                   @checked(in_array($value, $workDays, true))>
                                            {{ $label }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <p class="form-helper work-helper">
                                Hari yang tidak dicentang otomatis dianggap libur mingguan. Pengaturan ini dipakai lokasi kantor yang tidak mengisi hari kerjanya sendiri.
                            </p>
                        </div>
                    </div>

                    <div class="card-actions work-actions">
                        <button type="submit" class="btn btn-contained">Simpan Pengaturan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
