@extends('admin.layouts.app', [
    'activePage' => 'Surveyin',
])

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-body">
                <section id="basic-form-layouts">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-gradient-directional-info pl-2 pt-1 pb-1">
                                    <h4 class="card-title text-white" id="basic-layout-form">Add New Survey</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body p-1">
                                        <!-- Form untuk edit survey in -->
                                        <form id="editSurveyForm" enctype="multipart/form-data" method="post"
                                            action="{{ route('surveyin.update', $survey->kode_survey) }}">
                                            @csrf
                                            @method('PUT')

                                            {{-- No Container (readonly saat edit) --}}
                                            <div class="form-group">
                                                <label>No. Container</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $survey->no_container }}" readonly>
                                            </div>

                                            {{-- Status Container --}}
                                            <div class="form-group">
                                                <label>Status Container</label><br>
                                                @php $st = $survey->status_container; @endphp
                                                <label class="mr-2">
                                                    <input type="radio" name="status_container" value="AV"
                                                        {{ $st === 'AV' ? 'checked' : '' }}> AV
                                                </label>
                                                <label>
                                                    <input type="radio" name="status_container" value="DM"
                                                        {{ $st === 'DM' ? 'checked' : '' }}> DM
                                                </label>
                                            </div>

                                            {{-- DM Fields (toggle d-none saat AV) --}}
                                            <div id="dmContainerDiv"
                                                class="{{ $st === 'DM' ? '' : 'd-none' }} yellow-background">
                                                <div class="form-group">
                                                    <label>COMPONENT</label>
                                                    <select class="form-control select2" name="component"
                                                        style="width:100%;">
                                                        <option value="">--- Pilih Component ---</option>
                                                        @foreach ($component as $item)
                                                            <option value="{{ $item->cedex_code }}"
                                                                {{ old('component') === $item->cedex_code ? 'selected' : '' }}>
                                                                {{ $item->cedex_code }} - {{ $item->deskripsi }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                {{-- location, damage, repair, size_repair, qty, manhour, wh, labour_cost, material_cost, estimate_date --}}
                                                {{-- (prefill old() bila validation error) --}}
                                                <div class="form-group">
                                                    <label>LOCATION</label>
                                                    <input type="text" class="form-control" name="location"
                                                        value="{{ old('location') }}">
                                                </div>
                                                <div class="form-group">
                                                    <label>DAMAGE</label>
                                                    <select class="form-control select2" name="damage" style="width:100%;">
                                                        <option value="">--- Pilih Damage ---</option>
                                                        @foreach ($damage as $d)
                                                            <option value="{{ $d->cedex_code }}"
                                                                {{ old('damage') === $d->cedex_code ? 'selected' : '' }}>
                                                                {{ $d->cedex_code }} - {{ $d->deskripsi }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>REPAIR</label>
                                                    <select class="form-control select2" name="repair" style="width:100%;">
                                                        <option value="">--- Pilih Repair ---</option>
                                                        @foreach ($repair as $r)
                                                            <option value="{{ $r->cedex_code }}"
                                                                {{ old('repair') === $r->cedex_code ? 'selected' : '' }}>
                                                                {{ $r->cedex_code }} - {{ $r->deskripsi }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4"><label>SIZE</label><input class="form-control"
                                                            name="size_repair" value="{{ old('size_repair') }}"></div>
                                                    <div class="col-md-4"><label>QTY</label><input type="number"
                                                            class="form-control" name="qty"
                                                            value="{{ old('qty') }}"></div>
                                                    <div class="col-md-4"><label>MANHOUR</label><input class="form-control"
                                                            name="manhour" value="{{ old('manhour') }}"></div>
                                                </div>
                                                <div class="row mt-1">
                                                    <div class="col-md-4"><label>W/H</label><input class="form-control"
                                                            name="wh" value="{{ old('wh') }}"></div>
                                                    <div class="col-md-4"><label>Labour Cost</label><input type="number"
                                                            class="form-control" name="labour_cost"
                                                            value="{{ old('labour_cost') }}"></div>
                                                    <div class="col-md-4"><label>Material Cost</label><input type="number"
                                                            class="form-control" name="material_cost"
                                                            value="{{ old('material_cost') }}"></div>
                                                </div>
                                                <div class="form-group mt-1">
                                                    <label>Tgl Estimasi</label>
                                                    <input type="datetime-local" name="estimate_date" class="form-control"
                                                        value="{{ old('estimate_date') }}">
                                                </div>
                                            </div>

                                            {{-- Kegiatan LOLO/WASH/SWEEPING (selected) --}}
                                            <div class="form-group">
                                                <label>Service LO</label>
                                                <select class="select2 form-control" multiple name="kegiatan1[]"
                                                    style="width:100%;">
                                                    @foreach ($tarif_lolo_list as $t)
                                                        <option value="{{ $t->nama_jasa }}"
                                                            {{ in_array($t->nama_jasa, $kegiatanSelected) ? 'selected' : '' }}>
                                                            {{ $t->nama_jasa }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Service Wash</label>
                                                <select class="select2 form-control" multiple name="kegiatan2[]"
                                                    style="width:100%;">
                                                    @foreach ($tarif_wash_list as $t)
                                                        <option value="{{ $t->nama_jasa }}"
                                                            {{ in_array($t->nama_jasa, $kegiatanSelected) ? 'selected' : '' }}>
                                                            {{ $t->nama_jasa }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Service Sweeping</label>
                                                <select class="select2 form-control" multiple name="kegiatan3[]"
                                                    style="width:100%;">
                                                    @foreach ($tarif_sweeping_list as $t)
                                                        <option value="{{ $t->nama_jasa }}"
                                                            {{ in_array($t->nama_jasa, $kegiatanSelected) ? 'selected' : '' }}>
                                                            {{ $t->nama_jasa }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            {{-- Grade --}}
                                            @php $g = $survey->grade_container; @endphp
                                            <div class="form-group">
                                                <label>Grade Container</label><br>
                                                @foreach (['A', 'B', 'C', 'D', 'E'] as $grade)
                                                    <label class="mr-2">
                                                        <input type="radio" name="grade_container"
                                                            value="{{ $grade }}"
                                                            {{ $g === $grade ? 'checked' : '' }}>
                                                        {{ $grade }}
                                                    </label>
                                                @endforeach
                                            </div>

                                            {{-- Size/Tare/Payload/Max Gross --}}
                                            <div class="row">
                                                <div class="col-md-3"><label>Size</label>
                                                    <input type="text" class="form-control" name="sizze"
                                                        value="{{ $survey->sizze }}">
                                                </div>
                                                <div class="col-md-3"><label>Tare (KG)</label>
                                                    <input type="text" class="form-control" name="tare"
                                                        value="{{ $survey->tare }}">
                                                </div>
                                                <div class="col-md-3"><label>Payload (KG)</label>
                                                    <input type="text" class="form-control" name="payload"
                                                        value="{{ $survey->payload }}">
                                                </div>
                                                <div class="col-md-3"><label>Max. Gross (KG)</label>
                                                    <input type="text" class="form-control" name="max_gross"
                                                        value="{{ $survey->maxgross }}">
                                                </div>
                                            </div>

                                            {{-- No BL/DO (readonly) --}}
                                            <div class="form-group">
                                                <label>No BL/DO</label>
                                                <input type="text" name="no_bldo" value="{{ $survey->no_bldo }}"
                                                    class="form-control" disabled>
                                            </div>

                                            {{-- Truck/Driver/Trucking --}}
                                            <div class="form-group"><label>Nomor Truck</label>
                                                <input type="text" class="form-control" name="no_truck"
                                                    value="{{ $survey->no_truck }}">
                                            </div>
                                            <div class="form-group"><label>Nama Driver</label>
                                                <input type="text" class="form-control" name="driver"
                                                    value="{{ $survey->driver }}">
                                            </div>
                                            <div class="form-group"><label>Nama Trucking</label>
                                                <input type="text" class="form-control" name="nama_trucking"
                                                    value="{{ $survey->nama_trucking }}">
                                            </div>

                                            {{-- Tanggal IN Depo --}}
                                            <div class="form-group">
                                                <label>Tanggal IN Depo</label>
                                                <input type="datetime-local" name="tanggal_in_depo" class="form-control"
                                                    value="{{ optional($survey->tanggal_in_depo ? \Carbon\Carbon::parse($survey->tanggal_in_depo) : null)?->format('Y-m-d\TH:i') }}">
                                            </div>

                                            {{-- Foto Surat Jalan (preview + replace opsional) --}}
                                            <div class="form-group">
                                                <label>Foto Surat Jalan</label>
                                                @if ($survey->foto_surat_jalan)
                                                    <div class="mb-1">
                                                        <a href="{{ asset($survey->foto_surat_jalan) }}"
                                                            target="_blank">Lihat file saat ini</a>
                                                    </div>
                                                @endif
                                                <input type="file" class="form-control-file" name="foto_surat_jalan">
                                            </div>

                                            {{-- Lokasi Yard --}}
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label>Block</label>
                                                    <select class="form-control" id="blockSelect" name="block">
                                                        <option value="">--- Pilih Block ---</option>
                                                        @foreach ($blocks as $b)
                                                            <option value="{{ $b->block }}"
                                                                {{ $survey->block === $b->block ? 'selected' : '' }}>
                                                                {{ $b->block }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Slot</label>
                                                    <select class="form-control" id="slotSelect" name="slot">
                                                        <option value="">--- Pilih Slot ---</option>
                                                        @for ($i = 1; $i <= $maxSlot; $i++)
                                                            <option value="{{ $i }}"
                                                                {{ (string) $survey->slot === (string) $i ? 'selected' : '' }}>
                                                                {{ $i }}</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row mt-1">
                                                <div class="col-md-6">
                                                    <label>Row</label>
                                                    <select class="form-control" id="rowSelect" name="row2">
                                                        <option value="">--- Pilih Row ---</option>
                                                        @for ($i = 1; $i <= $maxRow; $i++)
                                                            <option value="{{ $i }}"
                                                                {{ (string) $survey->row2 === (string) $i ? 'selected' : '' }}>
                                                                {{ $i }}</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Tier</label>
                                                    <select class="form-control" id="tierSelect" name="tier">
                                                        <option value="">--- Pilih Tier ---</option>
                                                        @for ($i = 1; $i <= 4; $i++)
                                                            <option value="{{ $i }}"
                                                                {{ (string) $survey->tier === (string) $i ? 'selected' : '' }}>
                                                                {{ $i }}</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                            </div>

                                            {{-- Petunjuk Foto (sama seperti create) --}}
                                            {{-- ... --}}

                                            {{-- Foto Existing + Upload Baru --}}
                                            @php $bukti = json_decode($survey->bukti_photo ?? '[]', true); @endphp
                                            <div class="row">
                                                @foreach (['Tampak_Depan', 'Tampak_Samping_Kanan', 'Tampak_Samping_Kiri', 'Tampak_Belakang', 'Tampak_Atas', 'Tampak_MNFC', 'Tampak_Bawah'] as $pos)
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>{{ str_replace('_', ' ', $pos) }}</label>
                                                            {{-- daftar foto lama + checkbox hapus --}}
                                                            @if (!empty($bukti[$pos]))
                                                                <ul class="list-unstyled">
                                                                    @foreach ($bukti[$pos] as $path)
                                                                        <li class="mb-1">
                                                                            <a href="{{ asset($path) }}"
                                                                                target="_blank">{{ basename($path) }}</a>
                                                                            <label class="ml-1 text-danger small">
                                                                                <input type="checkbox"
                                                                                    name="bukti_remove[{{ $pos }}][]"
                                                                                    value="{{ $path }}">
                                                                                hapus
                                                                            </label>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            @endif
                                                            {{-- tambah foto baru --}}
                                                            <input type="file" class="form-control-file"
                                                                name="bukti_photo[{{ $pos }}][]" multiple>
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
            </div>
        </div>
    </div>
    {{-- Toggle DM --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const av = document.querySelector('input[name="status_container"][value="AV"]');
            const dm = document.querySelector('input[name="status_container"][value="DM"]');
            const div = document.getElementById('dmContainerDiv');

            function toggle() {
                dm.checked ? div.classList.remove('d-none') : div.classList.add('d-none');
            }
            av.addEventListener('change', toggle);
            dm.addEventListener('change', toggle);
            toggle();
        });

        // occupied* bisa diisi dari controller jika perlu filtering dinamis seperti create
        const occupiedSlots = @json($occupiedSlots);
        const occupiedRows = @json($occupiedRows);
        const occupiedTiers = @json($occupiedTiers);
    </script>
@endsection
