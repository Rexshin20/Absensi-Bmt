@push('styles')
    <link rel="stylesheet"
          href="{{ asset('assets/vendor/datatables/datatables.min.css') }}?v={{ filemtime(public_path('assets/vendor/datatables/datatables.min.css')) }}">
@endpush

@push('scripts')
    <script src="{{ asset('assets/vendor/datatables/jquery.min.js') }}?v={{ filemtime(public_path('assets/vendor/datatables/jquery.min.js')) }}"></script>
    <script src="{{ asset('assets/vendor/datatables/datatables.min.js') }}?v={{ filemtime(public_path('assets/vendor/datatables/datatables.min.js')) }}"></script>
    <script src="{{ asset('assets/js/tables.js') }}?v={{ filemtime(public_path('assets/js/tables.js')) }}"></script>
@endpush
