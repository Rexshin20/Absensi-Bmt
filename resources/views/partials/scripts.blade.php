<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.49.0/dist/apexcharts.min.js"></script>
<script src="{{ asset('assets/js/app.js') }}?v={{ filemtime(public_path('assets/js/app.js')) }}"></script>
<script src="{{ asset('assets/js/modal.js') }}?v={{ filemtime(public_path('assets/js/modal.js')) }}"></script>
@stack('scripts')
