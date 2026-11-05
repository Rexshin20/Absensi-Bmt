@extends('layouts.app')

@section('title', 'Lokasi Kantor')

@include('partials.datatables')


@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/leaflet/leaflet.css') }}">
    <style>
        .location-form-fields > * + * { margin-top: 1.25rem; }
        .location-actions { display: flex; align-items: center; justify-content: flex-end; gap: 0.25rem; }
        .location-actions form { display: inline-flex; }
        .location-name { display: block; color: var(--text-primary); font-weight: 500; }
        .location-coords { display: block; font-size: 0.8125rem; color: var(--text-secondary); line-height: 1.4; white-space: nowrap; }
        .locations-radius { white-space: nowrap; }

        .map-search { display: flex; align-items: flex-end; gap: 0.75rem; }
        .map-search .form-control { flex: 1 1 auto; min-width: 0; }
        .map-results { list-style: none; margin: 0.5rem 0 0; padding: 0; max-height: 220px; overflow-y: auto; border: 1px solid var(--divider); border-radius: 6px; background-color: var(--bg-paper); }
        .map-results[hidden] { display: none; }
        .map-results li + li { border-top: 1px solid var(--divider); }
        .map-results button { display: block; width: 100%; text-align: left; border: 0; background: transparent; padding: 0.625rem 0.875rem; color: var(--text-primary); font-size: 0.875rem; line-height: 1.5; cursor: pointer; }
        .map-results button:hover { background-color: var(--action-hover); }
        .location-map { height: 380px; margin-top: 0.75rem; border: 1px solid var(--divider); border-radius: 6px; }
        .map-footer { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem; margin-top: 0.5rem; }
        .map-coords { font-size: 0.8125rem; color: var(--text-secondary); }
        .map-message { margin: 0.5rem 0 0; font-size: 0.8125rem; color: var(--error-main); }
        .map-helper { margin: 0.35rem 0 0; }
        @media (max-width: 599px) {
            .location-map { height: 300px; }
            .map-search { flex-wrap: wrap; }
            .map-search .btn { width: 100%; }
        }
    </style>
@endpush

@section('content')
    <div class="grid">
        <div class="grid-item col-12">
            <div class="d-flex align-center justify-between flex-wrap gap-3">
                <div>
                    <h5 class="mb-1">Lokasi Kantor</h5>
                    <p class="text-body2 mb-0">Pilih titik kantor langsung di peta, lalu atur radius absennya.</p>
                </div>
                <div class="page-toolbar">
                    <span class="chip chip-sm">{{ $locations->count() }} lokasi</span>
                    <button type="button" class="btn btn-contained" data-modal-open="location-modal">
                        <x-icon name="Plus" size="18" class="mr-1" /> Tambah Lokasi
                    </button>
                </div>
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

        {{-- Table --}}
        <div class="grid-item col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-header-title" style="font-size: 1.25rem;">Daftar Lokasi</div>
                </div>
                <table class="table" data-table
                       data-empty="Belum ada lokasi kantor. Klik tombol Tambah Lokasi untuk membuat yang pertama."
                       style="min-width: 720px;" aria-label="daftar lokasi kantor">
                    <thead>
                        <tr>
                            <th>Lokasi</th>
                            <th>Alamat</th>
                            <th>Koordinat</th>
                            <th class="align-right" data-no-search>Radius</th>
                            <th class="align-center">Status</th>
                            <th class="align-right" data-no-sort data-no-search>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($locations as $location)
                            <tr>
                                <td>
                                    <span class="location-name">{{ $location->name }}</span>
                                </td>
                                <td><span class="text-body2">{{ $location->address }}</span></td>
                                <td>
                                    <span class="location-coords">{{ $location->latitude }}, {{ $location->longitude }}</span>
                                    <a href="https://www.google.com/maps?q={{ $location->latitude }},{{ $location->longitude }}"
                                       target="_blank" rel="noopener noreferrer" class="text-caption">Lihat di Maps</a>
                                </td>
                                <td class="align-right locations-radius" data-order="{{ $location->radius_m }}">{{ number_format($location->radius_m, 0, ',', '.') }} m</td>
                                <td class="align-center">
                                    @if ($location->is_active)
                                        <span class="chip chip-sm chip-success">Aktif</span>
                                    @else
                                        <span class="chip chip-sm">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="location-actions">
                                        <a href="{{ route('office-locations.index', ['edit' => $location->id]) }}"
                                           class="icon-btn icon-btn-sm" aria-label="edit {{ $location->name }}">
                                            <x-icon name="PencilOutline" size="20" />
                                        </a>
                                        <form method="POST" action="{{ route('office-locations.destroy', $location) }}"
                                              onsubmit="return confirm('Hapus lokasi {{ $location->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="icon-btn icon-btn-sm" aria-label="hapus {{ $location->name }}">
                                                <x-icon name="DeleteOutline" size="20" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal form --}}
    <x-crud-modal id="location-modal" wide
                  :title="$editLocation ? 'Edit Lokasi' : 'Tambah Lokasi'"
                  :subheader="$editLocation ? 'Perbarui data lokasi kantor.' : 'Cari lokasi kantor, lalu klik titiknya di peta.'"
                  :open="$editLocation || $errors->any()">
        <form method="POST"
              action="{{ $editLocation ? route('office-locations.update', $editLocation) : route('office-locations.store') }}"
              id="location-form">
            @csrf
            @if ($editLocation)
                @method('PUT')
            @endif

            <div class="modal-content">
                <div class="grid">
                    <div class="grid-item col-12 col-lg-5">
                        <div class="location-form-fields">
                            <div class="form-control">
                                <input id="name" name="name" type="text" placeholder=" "
                                       value="{{ old('name', $editLocation->name ?? '') }}"
                                       class="input @error('name') input-error @enderror" autocomplete="off">
                                <label class="form-label-float" for="name">Nama Lokasi</label>
                            </div>

                            <div class="form-control">
                                <textarea id="address" name="address" rows="3" placeholder=" "
                                          class="input @error('address') input-error @enderror">{{ old('address', $editLocation->address ?? '') }}</textarea>
                                <label class="form-label-float" for="address">Alamat</label>
                            </div>

                            <div>
                                <div class="form-control">
                                    <input id="radius_m" name="radius_m" type="number" step="1" min="10" max="10000" placeholder=" "
                                           value="{{ old('radius_m', $editLocation->radius_m ?? 150) }}"
                                           class="input @error('radius_m') input-error @enderror">
                                    <label class="form-label-float" for="radius_m">Radius (meter)</label>
                                </div>
                                <p class="form-helper">Jarak maksimal karyawan bisa absen dari titik kantor.</p>
                            </div>

                            <label class="checkbox-label">
                                <input type="checkbox" name="is_active" value="1"
                                       @checked(old('is_active', $editLocation->is_active ?? true))>
                                Lokasi aktif
                            </label>
                        </div>
                    </div>

                    <div class="grid-item col-12 col-lg-7">
                        <div class="map-search">
                            <div class="form-control">
                                <input id="map-search-input" type="search" placeholder=" " autocomplete="off"
                                       class="input">
                                <label class="form-label-float" for="map-search-input">Cari nama tempat atau alamat</label>
                            </div>
                            <button type="button" class="btn btn-outlined" id="map-search-button">Cari</button>
                        </div>

                        <ul class="map-results" id="map-results" hidden></ul>

                        <div id="location-map" class="location-map" aria-label="peta lokasi kantor"></div>

                        <p class="map-message" id="map-message" hidden></p>

                        <div class="map-footer">
                            <span class="map-coords" id="map-coords">Belum ada titik dipilih.</span>
                            <button type="button" class="btn btn-text btn-sm" id="use-current-location"
                                    style="text-transform: none; letter-spacing: 0;">
                                <x-icon name="CrosshairsGps" size="18" />
                                Gunakan lokasi saat ini
                            </button>
                        </div>
                        <p class="form-helper map-helper" style="margin-left: 0;">
                            Klik titik kantor di peta. Alamat terisi otomatis dan masih bisa diubah manual.
                        </p>

                        <input type="hidden" name="latitude" id="latitude"
                               value="{{ old('latitude', $editLocation->latitude ?? '') }}">
                        <input type="hidden" name="longitude" id="longitude"
                               value="{{ old('longitude', $editLocation->longitude ?? '') }}">
                    </div>
                </div>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn btn-text" data-modal-close>Batal</button>
                <button type="submit" class="btn btn-contained">
                    {{ $editLocation ? 'Simpan Perubahan' : 'Tambah Lokasi' }}
                </button>
            </div>
        </form>
    </x-crud-modal>
@endsection

@push('scripts')
    <script src="{{ asset('assets/vendor/leaflet/leaflet.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var mapElement = document.getElementById('location-map');
            if (!mapElement || typeof L === 'undefined') return;

            var form = document.getElementById('location-form');
            var latInput = document.getElementById('latitude');
            var lngInput = document.getElementById('longitude');
            var addressInput = document.getElementById('address');
            var radiusInput = document.getElementById('radius_m');
            var coordsLabel = document.getElementById('map-coords');
            var message = document.getElementById('map-message');
            var searchInput = document.getElementById('map-search-input');
            var searchButton = document.getElementById('map-search-button');
            var resultsList = document.getElementById('map-results');
            var currentLocationButton = document.getElementById('use-current-location');

            var initialLat = parseFloat(latInput.value);
            var initialLng = parseFloat(lngInput.value);
            var hasInitial = isFinite(initialLat) && isFinite(initialLng);

            var map = L.map(mapElement, { scrollWheelZoom: false })
                .setView(hasInitial ? [initialLat, initialLng] : [-2.5489, 118.0149], hasInitial ? 16 : 5);

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a>'
            }).addTo(map);

            var modal = document.getElementById('location-modal');
            modal.addEventListener('modal:opened', function () {
                setTimeout(function () { map.invalidateSize(); }, 80);
            });

            var marker = null;
            var circle = null;

            function showMessage(text) {
                message.textContent = text || '';
                message.hidden = !text;
            }

            function formatCoords(lat, lng) {
                return lat.toFixed(6) + ', ' + lng.toFixed(6);
            }

            function radiusValue() {
                var value = parseInt(radiusInput.value, 10);
                return isFinite(value) && value > 0 ? value : 150;
            }

            function updateCircle() {
                if (circle) circle.setRadius(radiusValue());
            }

            function setPoint(lat, lng) {
                var point = [lat, lng];

                if (marker) {
                    marker.setLatLng(point);
                } else {
                    marker = L.marker(point).addTo(map);
                }

                if (circle) {
                    circle.setLatLng(point);
                } else {
                    circle = L.circle(point, {
                        radius: radiusValue(),
                        color: '#9155FD',
                        weight: 2,
                        fillColor: '#9155FD',
                        fillOpacity: 0.12
                    }).addTo(map);
                }

                latInput.value = lat.toFixed(7);
                lngInput.value = lng.toFixed(7);
                coordsLabel.textContent = 'Titik: ' + formatCoords(lat, lng);
            }

            function clearResults() {
                resultsList.innerHTML = '';
                resultsList.hidden = true;
            }

            function fillAddressFromPoint(lat, lng) {
                if (addressInput.value.trim() && addressInput.dataset.auto !== '1') return;

                fetch('https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat='
                    + encodeURIComponent(lat) + '&lon=' + encodeURIComponent(lng) + '&accept-language=id')
                    .then(function (response) { return response.json(); })
                    .then(function (data) {
                        if (!data || !data.display_name) return;
                        if (addressInput.value.trim() && addressInput.dataset.auto !== '1') return;

                        addressInput.value = data.display_name.slice(0, 500);
                        addressInput.dataset.auto = '1';
                    })
                    .catch(function () {});
            }

            function moveTo(lat, lng, zoom) {
                map.flyTo([lat, lng], zoom || 16);
                setPoint(lat, lng);
            }

            function renderResults(items) {
                resultsList.innerHTML = '';

                items.forEach(function (item) {
                    var lat = parseFloat(item.lat);
                    var lng = parseFloat(item.lon);
                    if (!isFinite(lat) || !isFinite(lng)) return;

                    var li = document.createElement('li');
                    var button = document.createElement('button');
                    button.type = 'button';
                    button.textContent = item.display_name;
                    button.title = item.display_name;
                    button.addEventListener('click', function () {
                        moveTo(lat, lng, 16);
                        addressInput.value = item.display_name.slice(0, 500);
                        addressInput.dataset.auto = '1';
                        searchInput.value = '';
                        clearResults();
                        showMessage('');
                    });
                    li.appendChild(button);
                    resultsList.appendChild(li);
                });

                resultsList.hidden = resultsList.children.length === 0;
            }

            function searchLocation() {
                var query = searchInput.value.trim();

                if (!query) {
                    showMessage('Ketik nama tempat atau alamat yang dicari.');
                    return;
                }

                showMessage('');
                clearResults();
                searchButton.disabled = true;
                searchButton.textContent = 'Mencari...';

                fetch('https://nominatim.openstreetmap.org/search?format=jsonv2&limit=5&countrycodes=id'
                    + '&accept-language=id&q=' + encodeURIComponent(query))
                    .then(function (response) { return response.json(); })
                    .then(function (items) {
                        if (!items || items.length === 0) {
                            showMessage('Lokasi tidak ditemukan. Coba kata kunci lain.');
                            return;
                        }

                        renderResults(items);
                    })
                    .catch(function () {
                        showMessage('Gagal mencari lokasi. Periksa koneksi internet lalu coba lagi.');
                    })
                    .finally(function () {
                        searchButton.disabled = false;
                        searchButton.textContent = 'Cari';
                    });
            }

            map.on('click', function (event) {
                setPoint(event.latlng.lat, event.latlng.lng);
                fillAddressFromPoint(event.latlng.lat, event.latlng.lng);
                clearResults();
                showMessage('');
            });

            searchButton.addEventListener('click', searchLocation);

            searchInput.addEventListener('keydown', function (event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    searchLocation();
                }
            });

            radiusInput.addEventListener('input', updateCircle);

            addressInput.addEventListener('input', function () {
                addressInput.dataset.auto = '0';
            });

            currentLocationButton.addEventListener('click', function () {
                if (!navigator.geolocation) {
                    showMessage('Browser tidak mendukung pengambilan lokasi.');
                    return;
                }

                currentLocationButton.disabled = true;
                showMessage('');

                navigator.geolocation.getCurrentPosition(function (position) {
                    var lat = position.coords.latitude;
                    var lng = position.coords.longitude;

                    moveTo(lat, lng, 17);
                    fillAddressFromPoint(lat, lng);
                    currentLocationButton.disabled = false;
                }, function () {
                    showMessage('Gagal mengambil lokasi. Pastikan izin lokasi diizinkan.');
                    currentLocationButton.disabled = false;
                }, { enableHighAccuracy: true, timeout: 10000 });
            });

            form.addEventListener('submit', function (event) {
                if (!latInput.value || !lngInput.value) {
                    event.preventDefault();
                    showMessage('Klik titik lokasi kantor di peta terlebih dahulu.');
                    mapElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });

            if (hasInitial) {
                setPoint(initialLat, initialLng);
            }
        });
    </script>
@endpush
