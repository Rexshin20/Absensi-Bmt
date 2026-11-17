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

        .day-options { display: grid; grid-template-columns: repeat(auto-fit, minmax(6rem, 1fr)); gap: 0.5rem; }
        .day-option {
            display: flex; align-items: center; gap: 0.5rem; min-height: 2.5rem; padding: 0 0.75rem;
            border: 1px solid var(--divider); border-radius: 8px; background-color: var(--bg-paper);
            font-size: 0.8125rem; color: var(--text-primary); cursor: pointer;
            transition: border-color 150ms ease, background-color 150ms ease, color 150ms ease;
        }
        .day-option:hover { border-color: var(--primary-main); }
        .day-option:has(input:checked) { border-color: var(--primary-main); background-color: rgba(8, 127, 75, 0.08); color: var(--primary-dark); font-weight: 500; }
        .day-option input { margin: 0; accent-color: var(--primary-main); }

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
                       style="min-width: 840px;" aria-label="daftar lokasi kantor">
                    <thead>
                        <tr>
                            <th>Lokasi</th>
                            <th>Alamat</th>
                            <th>Koordinat</th>
                            <th class="align-right" data-no-search>Radius</th>
                            <th data-no-search>Jam Kerja</th>
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
                                <td>
                                    <span class="location-name">{{ $schedules[$location->id]['start'] }}</span>
                                    <span class="location-coords">{{ implode(' ', array_map(fn ($day) => $dayShort[$day] ?? '', $schedules[$location->id]['days'])) }}</span>
                                </td>
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
                            @php($selectedDays = array_map('intval', (array) old('work_days', $editLocation->work_days ?? [])))

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

                            <div>
                                <div class="form-control">
                                    <input id="work_start_time" name="work_start_time" type="time"
                                           value="{{ old('work_start_time', $editLocation->work_start_time ?? '') }}"
                                           class="input @error('work_start_time') input-error @enderror">
                                    <label class="form-label-float" for="work_start_time">Jam masuk (opsional)</label>
                                </div>
                                <p class="form-helper">Kosongkan untuk ikut pengaturan Hari Kerja.</p>
                            </div>

                            <div>
                                <span class="form-label">Hari kerja (opsional)</span>
                                <div class="day-options">
                                    @foreach ($days as $value => $label)
                                        <label class="day-option">
                                            <input type="checkbox" name="work_days[]" value="{{ $value }}"
                                                   @checked(in_array($value, $selectedDays, true))>
                                            {{ $label }}
                                        </label>
                                    @endforeach
                                </div>
                                <p class="form-helper" style="margin-left: 0;">Kosongkan semua untuk ikut pengaturan Hari Kerja.</p>
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
                                <label class="form-label-float" for="map-search-input">Cari tempat, koordinat, atau link Maps</label>
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
                            Ketik nama tempat untuk melihat saran otomatis. Bisa juga tempel koordinat
                            (mis. -7.795580, 110.369490) atau link Google Maps.
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

            var resolveLinkUrl = @json(route('office-locations.resolve-map'));
            var PHOTON_ENDPOINT = 'https://photon.komoot.io/api/';
            var NOMINATIM_ENDPOINT = 'https://nominatim.openstreetmap.org/';
            var AUTOCOMPLETE_DELAY = 350;
            var AUTOCOMPLETE_MIN_LENGTH = 3;

            var autocompleteTimer = null;
            var autocompleteController = null;
            var autocompleteSeq = 0;
            var reverseTimer = null;

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

                clearTimeout(reverseTimer);
                reverseTimer = setTimeout(function () {
                    fetch(NOMINATIM_ENDPOINT + 'reverse?format=jsonv2&lat='
                        + encodeURIComponent(lat) + '&lon=' + encodeURIComponent(lng) + '&accept-language=id')
                        .then(function (response) { return response.json(); })
                        .then(function (data) {
                            if (!data || !data.display_name) return;
                            if (addressInput.value.trim() && addressInput.dataset.auto !== '1') return;

                            addressInput.value = data.display_name.slice(0, 500);
                            addressInput.dataset.auto = '1';
                        })
                        .catch(function () {});
                }, 900);
            }

            function moveTo(lat, lng, zoom) {
                map.flyTo([lat, lng], zoom || 16);
                setPoint(lat, lng);
            }

            function normalizePoint(lat, lng) {
                var latitude = parseFloat(lat);
                var longitude = parseFloat(lng);

                if (!isFinite(latitude) || !isFinite(longitude)) return null;
                if (latitude < -90 || latitude > 90 || longitude < -180 || longitude > 180) return null;

                return { lat: latitude, lng: longitude };
            }

            function readDirectPoint(text) {
                if (!text) return null;

                var value = String(text).trim();
                var match = value.match(/^(-?\d{1,3}(?:\.\d+)?)\s*[,;]\s*(-?\d{1,3}(?:\.\d+)?)$/);

                if (match) return normalizePoint(match[1], match[2]);

                match = value.match(/@(-?\d{1,3}(?:\.\d+)?),(-?\d{1,3}(?:\.\d+)?)/);
                if (match) return normalizePoint(match[1], match[2]);

                match = value.match(/[?&](?:q|query|ll|center|destination)=loc:?(-?\d{1,3}(?:\.\d+)?),(-?\d{1,3}(?:\.\d+)?)/i);
                if (match) return normalizePoint(match[1], match[2]);

                match = value.match(/!3d(-?\d{1,3}(?:\.\d+)?)!4d(-?\d{1,3}(?:\.\d+)?)/);
                if (match) return normalizePoint(match[1], match[2]);

                return null;
            }

            function isMapLink(text) {
                return /^https?:\/\/(?:[a-z0-9-]+\.)*(?:goo\.gl|google\.[a-z.]+)\//i.test(String(text).trim());
            }

            function setSearching(active) {
                searchButton.disabled = active;
                searchButton.textContent = active ? 'Mencari...' : 'Cari';
            }

            function applyDirectPoint(point) {
                moveTo(point.lat, point.lng, 17);
                fillAddressFromPoint(point.lat, point.lng);
                searchInput.value = '';
                clearResults();
                showMessage('');
            }

            function photonLabel(properties) {
                var primary = properties.name || '';
                var streetLine = properties.street
                    ? properties.street + (properties.housenumber ? ' ' + properties.housenumber : '')
                    : '';

                if (primary && streetLine && primary.toLowerCase() !== streetLine.toLowerCase()) {
                    primary = primary + ', ' + streetLine;
                } else if (!primary) {
                    primary = streetLine || properties.city || properties.county || properties.state || 'Titik peta';
                }

                var parts = [primary];

                [properties.district, properties.city, properties.state].forEach(function (value) {
                    if (!value) return;
                    if (parts.join(', ').toLowerCase().indexOf(String(value).toLowerCase()) !== -1) return;
                    parts.push(value);
                });

                return parts.join(', ');
            }

            function renderResults(items) {
                resultsList.innerHTML = '';

                items.forEach(function (item) {
                    var point = normalizePoint(item.lat, item.lng);
                    if (!point) return;

                    var li = document.createElement('li');
                    var button = document.createElement('button');
                    button.type = 'button';
                    button.textContent = item.label;
                    button.title = item.label;
                    button.addEventListener('click', function () {
                        moveTo(point.lat, point.lng, 16);

                        if (item.address) {
                            addressInput.value = item.address.slice(0, 500);
                            addressInput.dataset.auto = '1';
                        } else {
                            fillAddressFromPoint(point.lat, point.lng);
                        }

                        searchInput.value = '';
                        clearResults();
                        showMessage('');
                    });
                    li.appendChild(button);
                    resultsList.appendChild(li);
                });

                resultsList.hidden = resultsList.children.length === 0;
            }

            function nominatimSearch(query) {
                fetch(NOMINATIM_ENDPOINT + 'search?format=jsonv2&limit=5&countrycodes=id'
                    + '&accept-language=id&q=' + encodeURIComponent(query))
                    .then(function (response) { return response.json(); })
                    .then(function (items) {
                        var results = (items || []).map(function (item) {
                            return {
                                lat: item.lat,
                                lng: item.lon,
                                label: item.display_name,
                                address: item.display_name
                            };
                        });

                        if (results.length === 0) {
                            showMessage('Lokasi tidak ditemukan. Coba kata kunci lain, atau tempel koordinat/link Google Maps.');
                            return;
                        }

                        renderResults(results);
                    })
                    .catch(function () {
                        showMessage('Gagal mencari lokasi. Periksa koneksi internet lalu coba lagi.');
                    })
                    .finally(function () { setSearching(false); });
            }

            function photonSearch(query, allowFallback) {
                if (autocompleteController) autocompleteController.abort();
                autocompleteController = typeof AbortController !== 'undefined' ? new AbortController() : null;

                var sequence = ++autocompleteSeq;
                var options = autocompleteController ? { signal: autocompleteController.signal } : undefined;

                fetch(PHOTON_ENDPOINT + '?limit=6&lat=-2.5&lon=118&q=' + encodeURIComponent(query), options)
                    .then(function (response) {
                        if (!response.ok) throw new Error('photon');
                        return response.json();
                    })
                    .then(function (data) {
                        if (sequence !== autocompleteSeq) return;

                        var items = ((data && data.features) || []).map(function (feature) {
                            var coordinates = (feature.geometry && feature.geometry.coordinates) || [];
                            var properties = feature.properties || {};
                            var label = photonLabel(properties);

                            return {
                                lat: coordinates[1],
                                lng: coordinates[0],
                                label: label,
                                address: label,
                                countrycode: properties.countrycode || ''
                            };
                        }).filter(function (item) {
                            return normalizePoint(item.lat, item.lng) !== null;
                        });

                        var localItems = items.filter(function (item) { return item.countrycode === 'ID'; });
                        var results = localItems.length ? localItems : items;

                        if (results.length === 0) {
                            if (allowFallback) {
                                nominatimSearch(query);
                                return;
                            }

                            clearResults();
                            return;
                        }

                        showMessage('');
                        renderResults(results);

                        if (allowFallback) setSearching(false);
                    })
                    .catch(function (error) {
                        if (error && error.name === 'AbortError') return;
                        if (sequence !== autocompleteSeq) return;

                        if (allowFallback) {
                            nominatimSearch(query);
                        }
                    });
            }

            function resolveMapLink(link) {
                showMessage('');
                clearResults();
                setSearching(true);

                fetch(resolveLinkUrl + '?url=' + encodeURIComponent(link), {
                    headers: { Accept: 'application/json' }
                })
                    .then(function (response) {
                        return response.json().then(function (body) {
                            return { ok: response.ok, body: body };
                        });
                    })
                    .then(function (result) {
                        if (!result.ok) {
                            showMessage(result.body && result.body.message
                                ? result.body.message
                                : 'Link Google Maps tidak bisa dibaca.');
                            return;
                        }

                        applyDirectPoint({ lat: result.body.latitude, lng: result.body.longitude });
                    })
                    .catch(function () {
                        showMessage('Gagal membaca link. Periksa koneksi internet lalu coba lagi.');
                    })
                    .finally(function () { setSearching(false); });
            }

            function searchLocation() {
                var query = searchInput.value.trim();

                if (!query) {
                    showMessage('Ketik nama tempat, tempel koordinat, atau link Google Maps.');
                    return;
                }

                var directPoint = readDirectPoint(query);

                if (directPoint) {
                    applyDirectPoint(directPoint);
                    return;
                }

                if (isMapLink(query)) {
                    resolveMapLink(query);
                    return;
                }

                showMessage('');
                clearResults();
                setSearching(true);
                photonSearch(query, true);
            }

            function scheduleAutocomplete() {
                clearTimeout(autocompleteTimer);

                var query = searchInput.value.trim();

                if (!query) {
                    clearResults();
                    return;
                }

                var directPoint = readDirectPoint(query);

                if (directPoint) {
                    renderResults([{
                        lat: directPoint.lat,
                        lng: directPoint.lng,
                        label: 'Gunakan koordinat ' + formatCoords(directPoint.lat, directPoint.lng),
                        address: ''
                    }]);
                    return;
                }

                if (query.length < AUTOCOMPLETE_MIN_LENGTH || isMapLink(query)) {
                    clearResults();
                    return;
                }

                autocompleteTimer = setTimeout(function () {
                    photonSearch(query, false);
                }, AUTOCOMPLETE_DELAY);
            }

            map.on('click', function (event) {
                setPoint(event.latlng.lat, event.latlng.lng);
                fillAddressFromPoint(event.latlng.lat, event.latlng.lng);
                clearResults();
                showMessage('');
            });

            searchButton.addEventListener('click', searchLocation);

            searchInput.addEventListener('input', scheduleAutocomplete);

            searchInput.addEventListener('paste', function (event) {
                var clipboard = event.clipboardData || window.clipboardData;
                var text = clipboard ? clipboard.getData('text') : '';
                var directPoint = readDirectPoint(text);

                if (directPoint) {
                    event.preventDefault();
                    applyDirectPoint(directPoint);
                    return;
                }

                if (isMapLink(text)) {
                    event.preventDefault();
                    resolveMapLink(text.trim());
                }
            });

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
