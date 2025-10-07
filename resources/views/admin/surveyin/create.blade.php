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
                                        <!-- Form untuk menambah survey -->
                                        <form id="addSurveyForm" enctype="multipart/form-data" method="post"
                                            action="{{ route('surveyin.store_detail') }}">
                                            @csrf
                                            <div class="form-group">
                                                <label for="noContainerSelect">Pilih No. Container</label>
                                                <select class="form-control" id="noContainerSelect" name="no_container">
                                                    <option value="">--- Pilih No Container ---</option>
                                                    @foreach ($gate_in as $data)
                                                        <option value="{{ $data->no_container }}">{{ $data->no_container }}
                                                            - {{ $data->no_bldo }} - {{ $data->size_type }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="statusRadio">Status Container</label><br>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" id="statusRadioAV"
                                                        name="status_container" value="AV">
                                                    <label class="form-check-label" for="statusRadioAV">AV</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" id="statusRadioDM"
                                                        name="status_container" value="DM">
                                                    <label class="form-check-label" for="statusRadioDM">DM</label>
                                                </div>
                                            </div>
                                            <div id="dmContainerDiv" class="d-none yellow-background">
                                                <div class="form-group">
                                                    <label for="componentInput" class="red-label">COMPONENT</label>
                                                    <select class="form-control select2" name="component"
                                                        id="componentInputcmp" style="width:100%;">
                                                        <option value="">--- Pilih Component ---</option>
                                                        @foreach ($component as $itemcmp)
                                                            <option value="{{ $itemcmp->cedex_code }}">
                                                                {{ $itemcmp->cedex_code }} - {{ $itemcmp->deskripsi }}-
                                                            </option>
                                                            <!-- Asumsi kolom 'id' dan 'name' ada di tabel cedex -->
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label for="locationInput" class="red-label">LOCATION</label>
                                                    <input type="text" class="form-control" name="location"
                                                        id="locationInput" style="width:100%;">
                                                </div>

                                                <div class="form-group">
                                                    <label for="componentInput" class="red-label">DAMAGE</label>
                                                    <select class="form-control select2" name="damage" id="damageInput"
                                                        style="width:100%;">
                                                        <option value="">--- Pilih Damage ---</option>
                                                        @foreach ($damage as $itemdmg)
                                                            <option value="{{ $itemdmg->cedex_code }}">
                                                                {{ $itemdmg->cedex_code }} - {{ $itemdmg->deskripsi }}-
                                                            </option>
                                                            <!-- Asumsi kolom 'id' dan 'name' ada di tabel cedex -->
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label for="componentInput" class="red-label">REPAIR</label>
                                                    <select class="form-control select2" name="repair" id="repairInput"
                                                        style="width:100%;">
                                                        <option value="">--- Pilih Repair ---</option>
                                                        @foreach ($repair as $itemrpr)
                                                            <option value="{{ $itemrpr->cedex_code }}">
                                                                {{ $itemrpr->cedex_code }} - {{ $itemrpr->deskripsi }}-
                                                            </option>
                                                            <!-- Asumsi kolom 'id' dan 'name' ada di tabel cedex -->
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="sizeInput" class="red-label">SIZE</label>
                                                    <input type="text" class="form-control" name="size_repair"
                                                        id="sizeInput" style="width:100%;">
                                                </div>
                                                <div class="form-group">
                                                    <label for="qtyInput" class="red-label">QTY</label>
                                                    <input type="number" class="form-control" name="qty" id="qtyInput"
                                                        style="width:100%;">
                                                </div>

                                                <div class="form-group">
                                                    <label for="manhourInput" class="red-label">MANHOUR</label>
                                                    <input type="text" class="form-control" name="manhour"
                                                        id="manhourInput" style="width:100%;">
                                                </div>
                                                <div class="form-group">
                                                    <label for="whInput" class="red-label">W/H</label>
                                                    <input type="text" class="form-control" name="wh"
                                                        id="whInput" style="width:100%;">
                                                </div>
                                                <div class="form-group">
                                                    <label for="labourCostInput" class="red-label">Labour Cost</label>
                                                    <input type="number" class="form-control" name="labour_cost"
                                                        id="labourCostInput" style="width:100%;">
                                                </div>
                                                <div class="form-group">
                                                    <label for="materialCostInput" class="red-label">Material Cost</label>
                                                    <input type="number" class="form-control" name="material_cost"
                                                        id="materialCostInput" style="width:100%;">
                                                </div>
                                                <div class="form-group">
                                                    <label for="estimate_date" class="red-label">Tgl Estimasi</label>
                                                    <input type="datetime-local" id="estimate_date" name="estimate_date"
                                                        class="form-control" style="width:100%;">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="noContainerSelect">Service LO</label>
                                                <select class="select2 form-control" multiple="multiple"
                                                    name="kegiatan1[]" id="containerSelect1" style="width:100%;">
                                                    @foreach ($tarif_lolo_list as $data)
                                                        <option value="{{ $data->nama_jasa }}">{{ $data->nama_jasa }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="noContainerSelect">Service Wash</label>
                                                <select class="select2 form-control" multiple="multiple"
                                                    name="kegiatan2[]" id="containerSelect2" style="width:100%;">
                                                    @foreach ($tarif_wash_list as $data)
                                                        <option value="{{ $data->nama_jasa }}">{{ $data->nama_jasa }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="noContainerSelect">Service Sweeping</label>
                                                <select class="select2 form-control" multiple="multiple"
                                                    name="kegiatan3[]" id="containerSelect3" style="width:100%;">
                                                    @foreach ($tarif_sweeping_list as $data)
                                                        <option value="{{ $data->nama_jasa }}">{{ $data->nama_jasa }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="statusRadio">Grade Container</label><br>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" id="statusRadio"
                                                        name="grade_container" value="A">
                                                    <label class="form-check-label" for="statusRadio">A</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" id="statusRadio2"
                                                        name="grade_container" value="B">
                                                    <label class="form-check-label" for="statusRadio2">B</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" id="statusRadio2"
                                                        name="grade_container" value="C">
                                                    <label class="form-check-label" for="statusRadio2">C</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" id="statusRadio2"
                                                        name="grade_container" value="D">
                                                    <label class="form-check-label" for="statusRadio2">D</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" id="statusRadio2"
                                                        name="grade_container" value="E">
                                                    <label class="form-check-label" for="statusRadio2">E</label>
                                                </div>
                                            </div>

                                            {{-- Letakkan setelah form-group 'Grade Container' --}}
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="size">Size</label>
                                                        <input type="text" id="sizze" class="form-control"
                                                            placeholder="Contoh: 20" name="sizze">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="tare">Tare (KG)</label>
                                                        <input type="text" id="tare" class="form-control"
                                                            placeholder="Contoh: 2200" name="tare">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="payload">Payload (KG)</label>
                                                        <input type="text" id="payload" class="form-control"
                                                            placeholder="Contoh: 28280" name="payload">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="max_gross">Max. Gross (KG)</label>
                                                        <input type="text" id="max_gross" class="form-control"
                                                            placeholder="Contoh: 30480" name="max_gross">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="no_bldo">No BL/DO</label>
                                                <input type="text" id="no_bldo" name="no_bldo" class="form-control"
                                                    disabled>
                                            </div>

                                            <div class="form-group">
                                                <label for="no_truck">Nomor Truck</label>
                                                <input type="text" id="no_truck" class="form-control"
                                                    placeholder="Masukkan nomor truck" name="no_truck">
                                            </div>

                                            <div class="form-group">
                                                <label for="driver">Nama Driver</label>
                                                <input type="text" id="driver" class="form-control"
                                                    placeholder="Masukkan nama driver" name="driver">
                                            </div>

                                            <div class="form-group">
                                                <label for="nama_trucking">Nama Trucking</label>
                                                <input type="text" id="nama_trucking" class="form-control"
                                                    value="" placeholder="Masukkan nama perusahaan trucking"
                                                    name="nama_trucking">
                                            </div>

                                            <div class="form-group">
                                                <label for="tanggal_in_depo">Tanggal IN Depo</label>
                                                <input type="datetime-local" id="tanggal_in_depo" name="tanggal_in_depo"
                                                    class="form-control" style="width:100%;">
                                            </div>

                                            <div class="form-group">
                                                <label for="photoFileSuratJalan">Foto Surat Jalan</label>
                                                <input type="file" class="form-control-file" id="photoFileSuratJalan"
                                                    name="foto_surat_jalan">
                                                @error('foto_surat_jalan')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <!-- <div class="form-group">
                                                    <label for="noContainerSelect">PIC Survey</label>
                                                    <select class="form-control" id="noContainerSelect" name="pic">
                                                        <option value="">--- Pilih PIC ---</option>
                                                        <option value="Alwi">Alwi</option>
                                                        <option value="Hasan">Hasan</option>
                                                        <option value="Jaka">Jaka</option>
                                                    </select>
                                                </div> -->
                                            <!-- penentuan lokasi penumpukan -->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="blockSelect">Block</label>
                                                        <select class="form-control" id="blockSelect" name="block">
                                                            <option value="">--- Pilih Block ---</option>
                                                            @foreach ($blocks as $block)
                                                                <option value="{{ $block->block }}">{{ $block->block }}
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
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="tierSelect">Tier</label>
                                                        <select class="form-control" id="tierSelect" name="tier">
                                                            <option value="">--- Pilih Tier ---</option>
                                                        </select>
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

                                            {{-- Di bawah ini adalah <div class="row"> untuk upload foto yang sudah ada --}}

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
                                                {{-- Tambahkan ini di dalam <div class="row"> upload foto yang terakhir --}}
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="photoFile7">Tampak Bawah (Under Structure)</label>
                                                        <input type="file" class="form-control-file" id="photoFile7"
                                                            name="bukti_photo[Tampak_Bawah]" multiple>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Save</button>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function updateOptions() {
                var block = document.getElementById('blockSelect').value;
                var slot = document.getElementById('slotSelect').value;
                var row = document.getElementById('rowSelect').value;


                console.log('Block yang dipilih:', block);
                console.log('Slot yang dipilih:', slot);
                console.log('Row yang dipilih:', row);

                var slotSelect = document.getElementById('slotSelect');
                var rowSelect = document.getElementById('rowSelect');
                var tierSelect = document.getElementById('tierSelect');

                // Update opsi slot
                Array.from(slotSelect.options).forEach(function(option) {
                    var optionSlot = option.value;
                    if (block && occupiedSlots[block + '_' + optionSlot]) {
                        option.remove();
                    }
                });

                // Update opsi row
                Array.from(rowSelect.options).forEach(function(option) {
                    var optionRow = option.value;
                    if (block && slot && occupiedRows[block + '_' + slot + '_' + optionRow]) {
                        option.remove();
                    }
                });

                // Update opsi tier
                tierSelect.innerHTML = '<option value="">--- Pilih Tier ---</option>';
                var maxTier = 0;
                if (block && slot && row) {
                    var key = block + '_' + slot + '_' + row;
                    if (occupiedTiers[key]) {
                        maxTier = occupiedTiers[key].max_tier;
                    }
                }

                // Tampilkan hanya tier yang tersedia
                for (var tier = 1; tier <= 4; tier++) { // Misalkan 4 adalah tier maksimum
                    if (tier > maxTier) {
                        var option = document.createElement('option');
                        option.value = tier;
                        option.text = tier;
                        tierSelect.appendChild(option);
                    }
                }
            }

            // Update opsi ketika block, slot, atau row berubah
            document.getElementById('blockSelect').addEventListener('change', updateOptions);
            document.getElementById('slotSelect').addEventListener('change', updateOptions);
            document.getElementById('rowSelect').addEventListener('change', updateOptions);

            // Pembaruan awal untuk opsi
            updateOptions();
        });
    </script>
    <!-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusRadioAV = document.getElementById('statusRadioAV');
            const statusRadioDM = document.getElementById('statusRadioDM');
            const dmContainerDiv = document.getElementById('dmContainerDiv');

            function toggleDMContainerDiv() {
                if (statusRadioDM.checked) {
                    dmContainerDiv.classList.remove('d-none');
                } else {
                    dmContainerDiv.classList.add('d-none');
                }
            }

            statusRadioAV.addEventListener('change', toggleDMContainerDiv);
            statusRadioDM.addEventListener('change', toggleDMContainerDiv);

            // Initial check in case the form is pre-populated
            toggleDMContainerDiv();
        });
    </script> -->

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const blockInput = document.getElementById('blockSelect');
            const slotInput = document.getElementById('slotSelect');
            const rowInput = document.getElementById('rowSelect');
            const tierInput = document.getElementById('tierSelect');

            function fetchMonitoringYardData() {
                const block = blockInput.value;
                const slot = slotInput.value;
                const row = rowInput.value;
                const tier = tierInput.value;

                if (block && slot && row && tier) {
                    fetch(`/admin/monitoringyard/get-monitoring-yard/${block}/${slot}/${row}/${tier}`)
                        .then(response => response.json())
                        .then(data => {
                            // Update the monitoring yard section with the received data
                            document.getElementById('monitoringYard').innerHTML = data.html;
                        })
                        .catch(error => console.error('Error:', error));
                }
            }

            blockInput.addEventListener('change', fetchMonitoringYardData);
            slotInput.addEventListener('change', fetchMonitoringYardData);
            rowInput.addEventListener('change', fetchMonitoringYardData);
            tierInput.addEventListener('change', fetchMonitoringYardData);
        });
    </script>

    <script>
        // Menangkap perubahan pada dropdown no_container
        document.getElementById('noContainerSelect').addEventListener('change', function() {
            var selectedNoContainer = this.value;

            // Mengambil nilai no_bldo sesuai dengan no_container yang dipilih
            @foreach ($gate_in as $data)
                if ("{{ $data->no_container }}" == selectedNoContainer) {
                    document.getElementById('no_bldo').value = "{{ $data->no_bldo }}";
                }
            @endforeach
        });
    </script>
@endsection
