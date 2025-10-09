@extends('admin.layouts.app', ['activePage' => 'Surveyin'])

@section('content')
    @php
        // Context edit
        $isEdit = true;
        $S = $survey ?? null;

        // Helper value() : old() → model → ''
        $val = function (string $name, $fallback = '') use ($S) {
            return old($name, $S->{$name} ?? $fallback);
        };

        // Helper datetime-local (YYYY-MM-DDTHH:MM)
        $dtVal = function (string $name) use ($S) {
            if (old($name)) {
                return old($name);
            }
            if (!$S || empty($S->{$name})) {
                return '';
            }
            try {
                return \Carbon\Carbon::parse($S->{$name})->format('Y-m-d\TH:i');
            } catch (\Throwable $e) {
                return '';
            }
        };

        // Selected kegiatan array dari DB (string "A, B") + old()
        $kegiatanSelected = collect($kegiatanSelected ?? []);
    @endphp

    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-body">
                <section id="basic-form-layouts">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-gradient-directional-info pl-2 pt-1 pb-1">
                                    <h4 class="card-title text-white" id="basic-layout-form">Edit Survey
                                        ({{ $S->kode_survey }})</h4>
                                </div>

                                <div class="card-content">
                                    <div class="card-body p-1">

                                        @if ($errors->any())
                                            <div class="alert alert-danger">
                                                <ul class="mb-0">
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif

                                        <form id="editSurveyForm" enctype="multipart/form-data" method="post"
                                            action="{{ route('surveyin.update', $S->kode_survey) }}">
                                            @csrf
                                            @method('PUT')

                                            {{-- No. Container (readonly) --}}
                                            <div class="form-group">
                                                <label>No. Container</label>
                                                <input type="text" class="form-control" value="{{ $S->no_container }}"
                                                    readonly>
                                            </div>

                                            {{-- Status Container --}}
                                            @php $st = old('status_container', $S->status_container ?? null); @endphp
                                            <div class="form-group">
                                                <label>Status Container</label><br>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" id="statusRadioAV"
                                                        name="status_container" value="AV"
                                                        {{ $st === 'AV' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="statusRadioAV">AV</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" id="statusRadioDM"
                                                        name="status_container" value="DM"
                                                        {{ $st === 'DM' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="statusRadioDM">DM</label>
                                                </div>
                                            </div>

                                            {{-- DM fields --}}
                                            <div id="dmContainerDiv"
                                                class="{{ $st === 'DM' ? '' : 'd-none' }} yellow-background">
                                                <div class="form-group">
                                                    <label class="red-label">COMPONENT</label>
                                                    <select class="form-control select2" name="component"
                                                        id="componentInputcmp" style="width:100%;">
                                                        <option value="">--- Pilih Component ---</option>
                                                        @foreach ($component as $itemcmp)
                                                            <option value="{{ $itemcmp->cedex_code }}"
                                                                {{ old('component') === $itemcmp->cedex_code ? 'selected' : '' }}>
                                                                {{ $itemcmp->cedex_code }} - {{ $itemcmp->deskripsi }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label class="red-label">LOCATION</label>
                                                    <input type="text" class="form-control" name="location"
                                                        value="{{ $val('location') }}">
                                                </div>

                                                <div class="form-group">
                                                    <label class="red-label">DAMAGE</label>
                                                    <select class="form-control select2" name="damage" id="damageInput"
                                                        style="width:100%;">
                                                        <option value="">--- Pilih Damage ---</option>
                                                        @foreach ($damage as $itemdmg)
                                                            <option value="{{ $itemdmg->cedex_code }}"
                                                                {{ old('damage') === $itemdmg->cedex_code ? 'selected' : '' }}>
                                                                {{ $itemdmg->cedex_code }} - {{ $itemdmg->deskripsi }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label class="red-label">REPAIR</label>
                                                    <select class="form-control select2" name="repair" id="repairInput"
                                                        style="width:100%;">
                                                        <option value="">--- Pilih Repair ---</option>
                                                        @foreach ($repair as $itemrpr)
                                                            <option value="{{ $itemrpr->cedex_code }}"
                                                                {{ old('repair') === $itemrpr->cedex_code ? 'selected' : '' }}>
                                                                {{ $itemrpr->cedex_code }} - {{ $itemrpr->deskripsi }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="red-label">SIZE</label>
                                                            <input type="text" class="form-control" name="size_repair"
                                                                value="{{ $val('size_repair') }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="red-label">QTY</label>
                                                            <input type="number" class="form-control" name="qty"
                                                                value="{{ $val('qty') }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="red-label">MANHOUR</label>
                                                            <input type="text" class="form-control" name="manhour"
                                                                value="{{ $val('manhour') }}">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="red-label">W/H</label>
                                                            <input type="text" class="form-control" name="wh"
                                                                value="{{ $val('wh') }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="red-label">Labour Cost</label>
                                                            <input type="number" class="form-control" name="labour_cost"
                                                                value="{{ $val('labour_cost') }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="red-label">Material Cost</label>
                                                            <input type="number" class="form-control"
                                                                name="material_cost" value="{{ $val('material_cost') }}">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="red-label">Tgl Estimasi</label>
                                                    <input type="datetime-local" name="estimate_date"
                                                        class="form-control" value="{{ $dtVal('estimate_date') }}">
                                                </div>
                                            </div>

                                            {{-- Service LOLO/WASH/SWEEPING --}}
                                            <div class="form-group">
                                                <label>Service LO</label>
                                                <select class="select2 form-control" multiple name="kegiatan1[]"
                                                    style="width:100%;">
                                                    @foreach ($tarif_lolo_list as $data)
                                                        @php
                                                            $isSel =
                                                                collect(old('kegiatan1', []))->contains(
                                                                    $data->nama_jasa,
                                                                ) || $kegiatanSelected->contains($data->nama_jasa);
                                                        @endphp
                                                        <option value="{{ $data->nama_jasa }}"
                                                            {{ $isSel ? 'selected' : '' }}>{{ $data->nama_jasa }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label>Service Wash</label>
                                                <select class="select2 form-control" multiple name="kegiatan2[]"
                                                    style="width:100%;">
                                                    @foreach ($tarif_wash_list as $data)
                                                        @php
                                                            $isSel =
                                                                collect(old('kegiatan2', []))->contains(
                                                                    $data->nama_jasa,
                                                                ) || $kegiatanSelected->contains($data->nama_jasa);
                                                        @endphp
                                                        <option value="{{ $data->nama_jasa }}"
                                                            {{ $isSel ? 'selected' : '' }}>{{ $data->nama_jasa }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label>Service Sweeping</label>
                                                <select class="select2 form-control" multiple name="kegiatan3[]"
                                                    style="width:100%;">
                                                    @foreach ($tarif_sweeping_list as $data)
                                                        @php
                                                            $isSel =
                                                                collect(old('kegiatan3', []))->contains(
                                                                    $data->nama_jasa,
                                                                ) || $kegiatanSelected->contains($data->nama_jasa);
                                                        @endphp
                                                        <option value="{{ $data->nama_jasa }}"
                                                            {{ $isSel ? 'selected' : '' }}>{{ $data->nama_jasa }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            {{-- Grade --}}
                                            @php $gr = old('grade_container', $S->grade_container ?? null); @endphp
                                            <div class="form-group">
                                                <label>Grade Container</label><br>
                                                @foreach (['A', 'B', 'C', 'D', 'E'] as $g)
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio"
                                                            name="grade_container" id="grade_{{ $g }}"
                                                            value="{{ $g }}"
                                                            {{ $gr === $g ? 'checked' : '' }}>
                                                        <label class="form-check-label"
                                                            for="grade_{{ $g }}">{{ $g }}</label>
                                                    </div>
                                                @endforeach
                                            </div>

                                            {{-- Teknis --}}
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Size</label>
                                                        <input type="text" name="sizze" class="form-control"
                                                            value="{{ $val('sizze', $S->sizze ?? '') }}"
                                                            placeholder="Contoh: 20">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Tare (KG)</label>
                                                        <input type="text" name="tare" class="form-control"
                                                            value="{{ $val('tare') }}" placeholder="Contoh: 2200">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Payload (KG)</label>
                                                        <input type="text" name="payload" class="form-control"
                                                            value="{{ $val('payload') }}" placeholder="Contoh: 28280">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Max. Gross (KG)</label>
                                                        <input type="text" name="max_gross" class="form-control"
                                                            value="{{ $val('max_gross', $S->maxgross ?? '') }}"
                                                            placeholder="Contoh: 30480">
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- No BL/DO (editable jika NULL) --}}
                                            @php $isNoBldoLocked = !empty(optional($S)->no_bldo); @endphp
                                            <div class="form-group">
                                                <label>No BL/DO</label>
                                                <input type="text" id="no_bldo" name="no_bldo" class="form-control"
                                                    value="{{ $val('no_bldo', $S->no_bldo ?? '') }}"
                                                    {{ $isNoBldoLocked ? 'readonly' : '' }}>
                                            </div>

                                            {{-- Trucking --}}
                                            <div class="form-group">
                                                <label>Nomor Truck</label>
                                                <input type="text" name="no_truck" class="form-control"
                                                    value="{{ $val('no_truck') }}" placeholder="Masukkan nomor truck">
                                            </div>
                                            <div class="form-group">
                                                <label>Nama Driver</label>
                                                <input type="text" name="driver" class="form-control"
                                                    value="{{ $val('driver') }}" placeholder="Masukkan nama driver">
                                            </div>
                                            <div class="form-group">
                                                <label>Nama Trucking</label>
                                                <input type="text" name="nama_trucking" class="form-control"
                                                    value="{{ $val('nama_trucking') }}"
                                                    placeholder="Masukkan nama perusahaan trucking">
                                            </div>

                                            {{-- Tanggal IN Depo --}}
                                            <div class="form-group">
                                                <label>Tanggal IN Depo</label>
                                                <input type="datetime-local" id="tanggal_in_depo" name="tanggal_in_depo"
                                                    class="form-control" value="{{ $dtVal('tanggal_in_depo') }}">
                                            </div>

                                            {{-- Foto Surat Jalan --}}
                                            <div class="form-group">
                                                <label>Foto Surat Jalan</label>
                                                @if ($S->foto_surat_jalan)
                                                    <div class="mb-1"><a href="{{ asset($S->foto_surat_jalan) }}"
                                                            target="_blank">Lihat file saat ini</a></div>
                                                @endif
                                                <input type="file" class="form-control-file" name="foto_surat_jalan">
                                                @error('foto_surat_jalan')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            {{-- Lokasi Penumpukan --}}
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="blockSelect">Block</label>
                                                        <select class="form-control" id="blockSelect" name="block">
                                                            <option value="">--- Pilih Block ---</option>
                                                            @foreach ($blocks as $block)
                                                                <option value="{{ $block->block }}"
                                                                    {{ old('block', $S->block ?? '') == $block->block ? 'selected' : '' }}>
                                                                    {{ $block->block }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="slotSelect">Slot</label>
                                                        <select class="form-control" id="slotSelect" name="slot">
                                                            <option value="">--- Pilih Slot ---</option>
                                                            @for ($i = 1; $i <= $maxSlot; $i++)
                                                                <option value="{{ $i }}"
                                                                    {{ (string) old('slot', $S->slot ?? '') === (string) $i ? 'selected' : '' }}>
                                                                    {{ $i }}
                                                                </option>
                                                            @endfor
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="rowSelect">Row</label>
                                                        <select class="form-control" id="rowSelect" name="row2">
                                                            <option value="">--- Pilih Row ---</option>
                                                            @for ($i = 1; $i <= $maxRow; $i++)
                                                                <option value="{{ $i }}"
                                                                    {{ (string) old('row2', $S->row2 ?? '') === (string) $i ? 'selected' : '' }}>
                                                                    {{ $i }}
                                                                </option>
                                                            @endfor
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="tierSelect">Tier</label>
                                                        <select class="form-control" id="tierSelect" name="tier">
                                                            <option value="">--- Pilih Tier ---</option>
                                                            @for ($i = 1; $i <= 4; $i++)
                                                                <option value="{{ $i }}"
                                                                    {{ (string) old('tier', $S->tier ?? '') === (string) $i ? 'selected' : '' }}>
                                                                    {{ $i }}
                                                                </option>
                                                            @endfor
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Petunjuk Foto --}}
                                            <div class="alert alert-info" role="alert">
                                                <h4 class="alert-heading">Petunjuk Dokumentasi Foto</h4>
                                                <p><strong>Umum:</strong> Wajib upload minimal 7 foto (Depan,
                                                    Belakang/Pintu, Samping Kanan, Samping Kiri, Atas, Bawah/Understructure,
                                                    MNFC).</p>
                                                <hr>
                                                <p class="mb-0"><strong>Khusus (jika ada):</strong></p>
                                                <ul>
                                                    <li><strong>Reefer:</strong> Foto kabel, plug in (socket), dan jenis
                                                        mesin.</li>
                                                    <li><strong>Open Top:</strong> Foto roof bow dan terpal.</li>
                                                    <li><strong>Flat Rack:</strong> Foto stanchion, twist lock, dan sliding
                                                        pin.</li>
                                                    <li><strong>ISO Tank:</strong> Foto tangga, valve atas & bawah, dan
                                                        thermometer.</li>
                                                </ul>
                                            </div>

                                            {{-- Foto Existing + Tambah Baru --}}
                                            @php $bukti = json_decode($S->bukti_photo ?? '[]', true) ?: []; @endphp
                                            @php $posList = ['Tampak_Depan','Tampak_Samping_Kanan','Tampak_Samping_Kiri','Tampak_Belakang','Tampak_Atas','Tampak_MNFC','Tampak_Bawah']; @endphp

                                            <div class="row">
                                                @foreach ($posList as $pos)
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>{{ str_replace('_', ' ', $pos) }}</label>

                                                            {{-- daftar foto lama --}}
                                                            @if (!empty($bukti[$pos]))
                                                                <ul class="list-unstyled">
                                                                    @foreach ($bukti[$pos] as $path)
                                                                        <li class="mb-1">
                                                                            <a href="{{ asset($path) }}"
                                                                                target="_blank">{{ basename($path) }}</a>
                                                                            <label class="ml-1 text-danger small">
                                                                                <input type="checkbox"
                                                                                    name="bukti_remove[{{ $pos }}][]"
                                                                                    value="{{ $path }}"> hapus
                                                                            </label>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            @endif

                                                            {{-- tambah foto baru --}}
                                                            <input type="file" class="form-control-file"
                                                                name="bukti_photo[{{ $pos }}][]" multiple>
                                                            @error('bukti_photo')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <button type="submit" class="btn btn-primary">Update</button>
                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Monitoring Yard placeholder (opsional) --}}
                <div id="monitoringYard"></div>
            </div>
        </div>
    </div>

    {{-- ===== Scripts ===== --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle DM section safely
            const av = document.querySelector('input[name="status_container"][value="AV"]');
            const dm = document.querySelector('input[name="status_container"][value="DM"]');
            const dmDiv = document.getElementById('dmContainerDiv');

            function toggleDM() {
                (dm && dm.checked) ? dmDiv.classList.remove('d-none'): dmDiv.classList.add('d-none');
            }
            if (av) av.addEventListener('change', toggleDM);
            if (dm) dm.addEventListener('change', toggleDM);
            toggleDM();

            // Occupancy data (from controller)
            const occupiedSlots = @json($occupiedSlots);
            const occupiedRows = @json($occupiedRows);
            const occupiedTiers = @json($occupiedTiers);

            const blockSel = document.getElementById('blockSelect');
            const slotSel = document.getElementById('slotSelect');
            const rowSel = document.getElementById('rowSelect');
            const tierSel = document.getElementById('tierSelect');

            function refreshTierOptions() {
                const block = blockSel.value,
                    slot = slotSel.value,
                    row = rowSel.value;
                const current = "{{ (string) old('tier', $S->tier ?? '') }}";
                tierSel.innerHTML = '<option value="">--- Pilih Tier ---</option>';
                let maxTier = 0;
                if (block && slot && row) {
                    const key = block + '_' + slot + '_' + row;
                    if (occupiedTiers[key]) {
                        maxTier = occupiedTiers[key].max_tier || 0;
                    }
                }
                for (let t = 1; t <= 4; t++) {
                    if (t > maxTier || String(t) === current) {
                        const opt = document.createElement('option');
                        opt.value = t;
                        opt.text = t;
                        if (String(t) === current) opt.selected = true;
                        tierSel.appendChild(opt);
                    }
                }
            }

            // Optional: fetch monitoring yard if all selected
            function fetchMonitoringYardData() {
                const b = blockSel.value,
                    s = slotSel.value,
                    r = rowSel.value,
                    t = tierSel.value;
                if (b && s && r && t) {
                    fetch(`/admin/monitoringyard/get-monitoring-yard/${b}/${s}/${r}/${t}`)
                        .then(res => res.json())
                        .then(data => {
                            document.getElementById('monitoringYard').innerHTML = data.html || '';
                        })
                        .catch(() => {});
                }
            }

            [blockSel, slotSel, rowSel].forEach(el => el && el.addEventListener('change', () => {
                refreshTierOptions();
                fetchMonitoringYardData();
            }));
            if (tierSel) tierSel.addEventListener('change', fetchMonitoringYardData);

            // Initial
            refreshTierOptions();
            fetchMonitoringYardData();
        });
    </script>
@endsection
