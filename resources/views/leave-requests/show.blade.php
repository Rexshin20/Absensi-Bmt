@extends('layouts.app')

@section('title', 'Detail Izin')

@push('styles')
    <style>
        .detail-back { display: inline-flex; align-items: center; gap: 0.25rem; margin-bottom: 0.75rem; color: var(--primary-main); font-size: 0.875rem; text-decoration: none; }
        .detail-back:hover { text-decoration: underline; }
        .leave-detail { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
        .leave-detail-full { grid-column: 1 / -1; }
        .leave-detail-label { font-size: 0.75rem; letter-spacing: 0.4px; color: var(--text-secondary); margin-bottom: 0.35rem; }
        .leave-detail-value { color: var(--text-primary); font-weight: 500; }
        .verify-divider { margin: 1.25rem 0; }
        .verify-delete { padding-left: 0; color: var(--error-main); text-transform: none; letter-spacing: 0; }
        @media (max-width: 600px) {
            .leave-detail { grid-template-columns: 1fr; }
        }
    </style>
@endpush

@section('content')
    <div class="grid">
        <div class="grid-item col-12">
            <a href="{{ route('leave-requests.index') }}" class="detail-back">
                <x-icon name="ArrowLeft" size="20" />
                Kembali ke daftar izin
            </a>
            <div class="d-flex align-center justify-between flex-wrap gap-3">
                <div>
                    <h5 class="mb-1">Detail Izin</h5>
                    <p class="text-body2 mb-0">Pengajuan izin {{ $leaveRequest->employee?->name ?? '—' }}.</p>
                </div>
                @php
                    $statusChip = [
                        'pending' => 'chip-warning',
                        'approved' => 'chip-success',
                        'rejected' => 'chip-error',
                    ][$leaveRequest->status] ?? '';
                @endphp
                <span class="chip chip-sm {{ $statusChip }}">{{ $leaveRequest->statusLabel() }}</span>
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

        @if (session('error'))
            <div class="grid-item col-12">
                <div class="alert alert-error">
                    <span class="alert-icon"><x-icon name="AlertCircleOutline" /></span>
                    <div class="alert-message">{{ session('error') }}</div>
                </div>
            </div>
        @endif

        <div class="grid-item col-12 col-lg-7">
            <div class="card">
                <div class="card-header">
                    <div class="card-header-title" style="font-size: 1.25rem;">Data Pengajuan</div>
                </div>
                <div class="card-content">
                    <div class="leave-detail">
                        <div>
                            <div class="leave-detail-label">Karyawan</div>
                            <div class="leave-detail-value">{{ $leaveRequest->employee?->name ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="leave-detail-label">Jenis Izin</div>
                            <div class="leave-detail-value">{{ $leaveRequest->leaveType?->name ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="leave-detail-label">Divisi</div>
                            <div class="leave-detail-value">{{ $leaveRequest->employee?->division?->name ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="leave-detail-label">Jabatan</div>
                            <div class="leave-detail-value">{{ $leaveRequest->employee?->position?->name ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="leave-detail-label">Tanggal Mulai</div>
                            <div class="leave-detail-value">{{ $leaveRequest->start_date->format('d/m/Y') }}</div>
                        </div>
                        <div>
                            <div class="leave-detail-label">Tanggal Selesai</div>
                            <div class="leave-detail-value">{{ $leaveRequest->end_date->format('d/m/Y') }}</div>
                        </div>
                        <div>
                            <div class="leave-detail-label">Hari Kerja</div>
                            <div class="leave-detail-value">{{ $leaveRequest->workingDays() }} hari</div>
                        </div>
                        <div>
                            <div class="leave-detail-label">Dicatat</div>
                            <div class="leave-detail-value">{{ $leaveRequest->created_at?->format('d/m/Y H:i') }}</div>
                        </div>
                        <div class="leave-detail-full">
                            <div class="leave-detail-label">Keterangan</div>
                            <div class="leave-detail-value">{{ $leaveRequest->note ?: '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid-item col-12 col-lg-5">
            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-header-title" style="font-size: 1.25rem;">Verifikasi</div>
                        <p class="card-header-subheader">
                            {{ $leaveRequest->status === 'pending' ? 'Tentukan hasil pengajuan izin ini.' : 'Hasil verifikasi pengajuan izin.' }}
                        </p>
                    </div>
                </div>
                <div class="card-content">
                    @if ($leaveRequest->status === 'pending')
                        <p class="text-body2">Pengajuan ini menunggu keputusan admin.</p>

                        <form method="POST" action="{{ route('leave-requests.approve', $leaveRequest) }}" class="mt-3">
                            @csrf
                            <button type="submit" class="btn btn-contained btn-full">
                                Setujui Pengajuan
                            </button>
                        </form>

                        <div class="divider-text verify-divider"><span>atau</span></div>

                        @if ($errors->any())
                            <div class="alert alert-error mb-5">
                                <span class="alert-icon"><x-icon name="AlertCircleOutline" /></span>
                                <div class="alert-message">
                                    <div class="alert-title">Data belum valid</div>
                                    <ul class="mb-0" style="padding-left: 1.125rem;">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('leave-requests.reject', $leaveRequest) }}">
                            @csrf
                            <div class="form-control">
                                <textarea id="rejection_reason" name="rejection_reason" rows="3" placeholder=" "
                                          class="input @error('rejection_reason') input-error @enderror">{{ old('rejection_reason') }}</textarea>
                                <label class="form-label-float" for="rejection_reason">Alasan penolakan</label>
                            </div>
                            <button type="submit" class="btn btn-outlined btn-error btn-full mt-3">
                                Tolak Pengajuan
                            </button>
                        </form>
                    @elseif ($leaveRequest->status === 'approved')
                        <div class="alert alert-success">
                            <span class="alert-icon"><x-icon name="CheckCircleOutline" /></span>
                            <div class="alert-message">
                                <div class="alert-title">Pengajuan disetujui</div>
                                Tercatat sebagai izin {{ $leaveRequest->leaveType?->name ?? '' }} selama {{ $leaveRequest->workingDays() }} hari kerja.
                            </div>
                        </div>
                        <p class="text-body2 mb-0">
                            Diproses oleh {{ $leaveRequest->verifier?->name ?? 'admin' }}
                            @if ($leaveRequest->verified_at)
                                pada {{ $leaveRequest->verified_at->format('d/m/Y H:i') }}
                            @endif
                        </p>
                    @else
                        <div class="alert alert-error">
                            <span class="alert-icon"><x-icon name="CloseCircleOutline" /></span>
                            <div class="alert-message">
                                <div class="alert-title">Pengajuan ditolak</div>
                                {{ $leaveRequest->rejection_reason ?: 'Tidak ada alasan penolakan.' }}
                            </div>
                        </div>
                        <p class="text-body2 mb-0">
                            Diproses oleh {{ $leaveRequest->verifier?->name ?? 'admin' }}
                            @if ($leaveRequest->verified_at)
                                pada {{ $leaveRequest->verified_at->format('d/m/Y H:i') }}
                            @endif
                        </p>
                    @endif

                    <div class="divider verify-divider"></div>

                    <form method="POST" action="{{ route('leave-requests.destroy', $leaveRequest) }}"
                          onsubmit="return confirm('Hapus catatan izin ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-text verify-delete">
                            <x-icon name="DeleteOutline" size="20" />
                            Hapus catatan izin
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
