@extends('admin.layouts.app', [
    'activePage' => 'Surveyout',
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
                                    <h4 class="card-title text-white" id="basic-layout-form">Add New Survey Out</h4>
                                </div>

                                <div class="card-content">
                                    <div class="card-body p-1">
                                        <form id="addSurveyForm" enctype="multipart/form-data" method="post"
                                            action="{{ route('surveyout.store') }}">
                                            @csrf

                                            @if (session('error'))
                                                <div class="alert alert-danger">{{ session('error') }}</div>
                                            @endif
                                            @if ($errors->any())
                                                <div class="alert alert-danger">
                                                    <ul class="mb-0">
                                                        @foreach ($errors->all() as $error)
                                                            <li>{{ $error }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif

                                            {{-- Pilih No. Container dari hasil Survey IN (kombinasi no_container+BLDO yang eligible) --}}
                                            <div class="form-group">
                                                <label for="noContainerSelect">Pilih No. Container (Referensi)</label>
                                                <select class="form-control" id="noContainerSelect" name="no_container"
                                                    required>
                                                    <option value="">--- Pilih No Container ---</option>
                                                    @foreach ($datasurveyout as $row)
                                                        <option value="{{ $row->no_container }}"
                                                            data-bldo="{{ $row->no_bldo ?? '' }}"
                                                            data-size="{{ $row->size_type ?? '' }}"
                                                            data-jenis="{{ $row->jenis_container ?? '' }}"
                                                            data-sizze="{{ $row->sizze ?? '' }}"
                                                            data-payload="{{ $row->payload ?? '' }}"
                                                            data-tare="{{ $row->tare ?? '' }}">
                                                            {{ $row->no_container }}{{ $row->no_bldo ? ' - ' . $row->no_bldo : '' }}{{ $row->size_type ? ' - ' . $row->size_type : '' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <small class="text-muted">Ini referensi data dari sistem (Survey
                                                    IN).</small>
                                            </div>

                                            {{-- Ringkasan dari container terpilih (Readonly) --}}
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="no_bldo">No BL/DO (Referensi)</label>
                                                        <input type="text" id="no_bldo" class="form-control" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="size_type">Size Type (Referensi)</label>
                                                        <input type="text" id="size_type" class="form-control" disabled>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- =========================
                            FIELD TAMBAHAN (TALLY)
                            ========================= --}}
                                            <div class="card border mt-2 mb-2">
                                                <div class="card-body p-1">
                                                    <h6 class="mb-1">Input Manual (Tally)</h6>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="sender_code">SENDER_CODE</label>
                                                                <select id="sender_code" name="sender_code"
                                                                    class="form-control">
                                                                    <option value="MV12">MV12</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="movement">MOVEMENT</label>
                                                                <select id="movement" name="movement" class="form-control">
                                                                    <option value="">-- Pilih --</option>
                                                                    <option value="BOKINGAN">BOKINGAN</option>
                                                                    <option value="REPO">REPO</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="ef">E/F</label>
                                                                <select id="ef" name="ef" class="form-control">
                                                                    <option value="">-- Pilih --</option>
                                                                    <option value="EMPTY">EMPTY</option>
                                                                    <option value="FULL">FULL</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="no_booking">NO_BOOKING</label>
                                                                <input type="text" id="no_booking" name="no_booking"
                                                                    class="form-control" placeholder="Boleh kosong">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="vessel_code">VESSEL Code</label>
                                                                <input type="text" id="vessel_code" name="vessel_code"
                                                                    class="form-control" placeholder="Boleh kosong">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="voyage">VOYAGE</label>
                                                                <input type="text" id="voyage" name="voyage"
                                                                    class="form-control" placeholder="Boleh kosong">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Auto-isi dari SURVEYIN (disabled + hidden mirror agar terkirim) --}}
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="sizze_display">SIZE</label>
                                                                <input type="text" id="sizze_display"
                                                                    class="form-control" placeholder="cth: 20" disabled>
                                                                <input type="hidden" id="sizze" name="sizze">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="payload_display">PAYLOAD (kg)</label>
                                                                <input type="text" id="payload_display"
                                                                    class="form-control" placeholder="kg" disabled>
                                                                <input type="hidden" id="payload" name="payload">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="tare_display">TARE (kg)</label>
                                                                <input type="text" id="tare_display"
                                                                    class="form-control" placeholder="kg" disabled>
                                                                <input type="hidden" id="tare" name="tare">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="remark">REMARK</label>
                                                                <input type="text" id="remark" name="remark"
                                                                    class="form-control" placeholder="Boleh kosong">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="shipper">SHIPPER</label>
                                                                <input type="text" id="shipper" name="shipper"
                                                                    class="form-control" placeholder="Boleh kosong">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="seal">SEAL</label>
                                                                <input type="text" id="seal" name="seal"
                                                                    class="form-control" placeholder="Boleh kosong">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Status Container (AV/DM) --}}
                                            <div class="form-group">
                                                <label for="statusRadio">Status Container</label><br>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" id="statusRadioAV"
                                                        name="status_container" value="AV" required>
                                                    <label class="form-check-label" for="statusRadioAV">AV</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" id="statusRadioDM"
                                                        name="status_container" value="DM" required>
                                                    <label class="form-check-label" for="statusRadioDM">DM</label>
                                                </div>
                                            </div>

                                            {{-- Grade Container (A-E) --}}
                                            <div class="form-group">
                                                <label for="statusRadio">Grade Container</label><br>
                                                @foreach (['A', 'B', 'C', 'D', 'E'] as $g)
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio"
                                                            id="grade{{ $g }}" name="grade_container"
                                                            value="{{ $g }}">
                                                        <label class="form-check-label"
                                                            for="grade{{ $g }}">{{ $g }}</label>
                                                    </div>
                                                @endforeach
                                            </div>

                                            {{-- Mode Keluar: WO / NOTA --}}
                                            <div class="form-group">
                                                <label for="mode_keluar">Mode Keluar</label>
                                                <select class="form-control" id="mode_keluar" name="mode_keluar"
                                                    required>
                                                    <option value="">-- Pilih Mode --</option>
                                                    <option value="WO">WO</option>
                                                    <option value="NOTA">NOTA</option>
                                                </select>
                                            </div>

                                            {{-- Informasi kendaraan --}}
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="no_truck">Nomor Truck</label>
                                                        <input type="text" id="no_truck" class="form-control"
                                                            placeholder="Masukkan nomor truck" name="no_truck">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="driver">Nama Driver</label>
                                                        <input type="text" id="driver" class="form-control"
                                                            placeholder="Masukkan nama driver" name="driver">
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Letakkan sebelum <div class="row"> untuk upload foto --}}
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

                                            {{-- Upload foto --}}
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="photoFile1">Tampak Depan</label>
                                                        <input type="file" class="form-control-file" id="photoFile1"
                                                            name="bukti_photo[Tampak_Depan]" multiple>
                                                        @error('bukti_photo')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="photoFile2">Tampak Samping Kanan</label>
                                                        <input type="file" class="form-control-file" id="photoFile2"
                                                            name="bukti_photo[Tampak_Samping_Kanan]" multiple>
                                                        @error('bukti_photo')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="photoFile3">Tampak Samping Kiri</label>
                                                        <input type="file" class="form-control-file" id="photoFile3"
                                                            name="bukti_photo[Tampak_Samping_Kiri]" multiple>
                                                        @error('bukti_photo')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="photoFile4">Tampak Belakang</label>
                                                        <input type="file" class="form-control-file" id="photoFile4"
                                                            name="bukti_photo[Tampak_Belakang]" multiple>
                                                        @error('bukti_photo')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="photoFile5">Tampak Atas</label>
                                                        <input type="file" class="form-control-file" id="photoFile5"
                                                            name="bukti_photo[Tampak_Atas]" multiple>
                                                        @error('bukti_photo')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="photoFile6">Tampak MNFC</label>
                                                        <input type="file" class="form-control-file" id="photoFile6"
                                                            name="bukti_photo[Tampak_MNFC]" multiple>
                                                        @error('bukti_photo')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                {{-- Tambahan: Under Structure --}}
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="photoFile7">Tampak Bawah (Under Structure)</label>
                                                        <input type="file" class="form-control-file" id="photoFile7"
                                                            name="bukti_photo[Tampak_Bawah]" multiple>
                                                    </div>
                                                </div>
                                            </div>

                                            <button type="submit" class="btn btn-primary">Save</button>
                                            <a href="{{ route('surveyout.index') }}"
                                                class="btn btn-secondary">Kembali</a>

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

    {{-- Script: auto-isi BL/DO, Size Type, + Size/ Payload/ Tare (disabled + hidden) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dd = document.getElementById('noContainerSelect');
            const bldo = document.getElementById('no_bldo');
            const sizeType = document.getElementById('size_type');

            // bisa jadi field jenis_container tidak ada di layout saat ini — jadi amanin
            const jenisEl = document.getElementById('jenis_container');

            // display (disabled) + hidden mirrors
            const sizzeDisp = document.getElementById('sizze_display');
            const payloadDisp = document.getElementById('payload_display');
            const tareDisp = document.getElementById('tare_display');

            const sizzeHid = document.getElementById('sizze');
            const payloadHid = document.getElementById('payload');
            const tareHid = document.getElementById('tare');

            function fillInfo() {
                const opt = dd.options[dd.selectedIndex];
                if (!opt) return;

                const vBldo = opt.getAttribute('data-bldo') || '';
                const vSize = opt.getAttribute('data-size') || '';
                const vJenis = opt.getAttribute('data-jenis') || '';
                const vSizze = opt.getAttribute('data-sizze') || '';
                const vPayload = opt.getAttribute('data-payload') || '';
                const vTare = opt.getAttribute('data-tare') || '';

                if (bldo) bldo.value = vBldo;
                if (sizeType) sizeType.value = vSize;
                if (jenisEl) jenisEl.value = vJenis;

                // set display (disabled) + hidden mirrors
                if (sizzeDisp) sizzeDisp.value = vSizze;
                if (payloadDisp) payloadDisp.value = vPayload;
                if (tareDisp) tareDisp.value = vTare;

                if (sizzeHid) sizzeHid.value = vSizze;
                if (payloadHid) payloadHid.value = vPayload;
                if (tareHid) tareHid.value = vTare;
            }

            dd.addEventListener('change', fillInfo);
            // isi awal jika browser menyimpan pilihan
            fillInfo();
        });
    </script>
@endsection
