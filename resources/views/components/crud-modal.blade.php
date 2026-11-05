@props([
    'id',
    'title',
    'subheader' => null,
    'open' => false,
    'wide' => false,
])

<div class="modal-root {{ $wide ? 'modal-wide' : '' }}" id="{{ $id }}" data-open="{{ $open ? 1 : 0 }}">
    <div class="modal-backdrop" data-modal-close></div>

    <div class="modal-paper" role="dialog" aria-modal="true" aria-labelledby="{{ $id }}-title">
        <div class="modal-header">
            <div>
                <div class="modal-title" id="{{ $id }}-title">{{ $title }}</div>
                @if ($subheader)
                    <p class="modal-subheader">{{ $subheader }}</p>
                @endif
            </div>
            <button type="button" class="icon-btn icon-btn-sm" data-modal-close aria-label="tutup">
                <x-icon name="Close" />
            </button>
        </div>

        @if ($errors->any())
            <div class="modal-content">
                <div class="alert alert-error">
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
            </div>
        @endif

        {{ $slot }}
    </div>
</div>
