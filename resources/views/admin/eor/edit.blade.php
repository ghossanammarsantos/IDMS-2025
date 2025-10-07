@extends('admin.layouts.app', [
    'activePage' => 'Master',
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
                                    <h4 class="card-title text-white" id="basic-layout-form">Edit Estimate Of Repair</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body p-1">

                                        <form action="{{ route('eor.update', $eor->eor_code) }}" method="POST">
                                            @csrf
                                            {{-- @method('PUT')  // aktifkan jika route update memakai PUT/PATCH --}}

                                            {{-- Row 1 --}}
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="eor_code">Kode EOR</label>
                                                        <input type="text" id="eor_code" name="eor_code" class="form-control"
                                                               value="{{ $eor->eor_code }}" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="no_container">No. Container</label>
                                                        <input type="text" id="no_container" name="no_container" class="form-control"
                                                               value="{{ $eor->no_container }}" disabled>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Row 2 --}}
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="container_size">Ukuran Container</label>
                                                        <input type="text" id="container_size" name="ukuran_container" class="form-control"
                                                               value="{{ $eor->size_type }}" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="gate_in">Waktu Gate In</label>
                                                        <input type="datetime-local" id="gate_in" name="gatein_time" class="form-control"
                                                               value="{{ $eor->gatein_time ? date('Y-m-d\TH:i', strtotime($eor->gatein_time)) : '' }}" disabled>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Row 3 --}}
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="pic_gatein">PIC Gate In</label>
                                                        <input type="text" id="pic_gatein" name="pic_gatein" class="form-control"
                                                               value="{{ $eor->pic_gatein }}" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="no_bldo">No BL/DO</label>
                                                        <input type="text" id="no_bldo" name="no_bldo" class="form-control"
                                                               value="{{ $eor->no_bldo }}" disabled>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Row 4 --}}
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="vessel">Vessel</label>
                                                        <input type="text" id="vessel" name="vessel" class="form-control"
                                                               value="{{ $eor->vessel }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="voyage">Voyage</label>
                                                        <input type="text" id="voyage" name="voyage" class="form-control"
                                                               value="{{ $eor->voyage }}">
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Row 5 --}}
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="shipper">Shipper</label>
                                                        <input type="text" id="shipper" name="shipper" class="form-control"
                                                               value="{{ $eor->shipper }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="survey_time">Waktu Survey</label>
                                                        <input type="datetime-local" id="survey_time" name="survey_time" class="form-control"
                                                               value="{{ $eor->survey_time ? date('Y-m-d\TH:i', strtotime($eor->survey_time)) : '' }}" disabled>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Row 6 --}}
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="pic_survey">PIC Survey</label>
                                                        <input type="text" id="pic_survey" name="pic_survey" class="form-control"
                                                               value="{{ $eor->pic_survey }}" disabled>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Row 7 --}}
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="date_started">Tgl Dikerjakan</label>
                                                        <input type="datetime-local" id="date_started" name="date_started" class="form-control"
                                                               value="{{ $eor->date_started ? date('Y-m-d\TH:i', strtotime($eor->date_started)) : '' }}" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="date_completed">Tgl Selesai</label>
                                                        <input type="datetime-local" id="date_completed" name="date_completed" class="form-control"
                                                               value="{{ $eor->date_completed ? date('Y-m-d\TH:i', strtotime($eor->date_completed)) : '' }}" disabled>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Tabs --}}
                                            <div>
                                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link active" id="demage-tab" data-toggle="tab" data-target="#demage" type="button" role="tab" aria-controls="demage" aria-selected="true">Demage</button>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link" id="gateinpic-tab" data-toggle="tab" data-target="#gateinpic" type="button" role="tab" aria-controls="gateinpic" aria-selected="false">Gate In PIC</button>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link" id="takeaction-tab" data-toggle="tab" data-target="#takeaction" type="button" role="tab" aria-controls="takeaction" aria-selected="false">Take Action</button>
                                                    </li>
                                                </ul>

                                                <div class="tab-content" id="myTabContent">
                                                    {{-- Tab Demage --}}
                                                    <div class="tab-pane fade show active" id="demage" role="tabpanel" aria-labelledby="demage-tab">
                                                        <div class="table-responsive">
                                                            <table class="table table-hover table-bordered">
                                                                <thead class="thead-dark">
                                                                    <tr>
                                                                        <th>Kode EOR</th>
                                                                        <th>Kode Survey</th>
                                                                        <th>No Container</th>
                                                                        <th>COMP</th>
                                                                        <th>LOC</th>
                                                                        <th>DMG</th>
                                                                        <th>RPR</th>
                                                                        <th>SIZE</th>
                                                                        <th>QTY</th>
                                                                        <th>Manhour</th>
                                                                        <th>Labour Cost</th>
                                                                        <th>Material Cost</th>
                                                                        <th>Aksi</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @forelse($eor_details as $detail)
                                                                        <tr>
                                                                            <td>{{ $detail->eor_code }}</td>
                                                                            <td>{{ $detail->kode_survey }}</td>
                                                                            <td>{{ $detail->no_container }}</td>
                                                                            <td>{{ $detail->component }}</td>
                                                                            <td>{{ $detail->location }}</td>
                                                                            <td>{{ $detail->damage }}</td>
                                                                            <td>{{ $detail->repair }}</td>
                                                                            <td>{{ $detail->size_repair }}</td>
                                                                            <td>{{ $detail->qty }}</td>
                                                                            <td>{{ $detail->manhour }}</td>
                                                                            <td>{{ $detail->labour_cost }}</td>
                                                                            <td>{{ $detail->material_cost }}</td>
                                                                            <td>
                                                                                <a class="btn btn-warning btn-sm"
                                                                                   href="{{ route('eor.editeordetail', ['eor_code' => $detail->eor_code]) }}">
                                                                                    <i class="fa fa-edit"></i> Edit
                                                                                </a>
                                                                            </td>
                                                                        </tr>
                                                                    @empty
                                                                        <tr>
                                                                            <td colspan="13" class="text-center">Tidak ada data detail.</td>
                                                                        </tr>
                                                                    @endforelse
                                                                </tbody>
                                                                @php
                                                                    $grandTotal = collect($eor_details ?? [])->sum(function($d) {
                                                                        $tc  = is_object($d) ? ($d->total_cost ?? null) : ($d['total_cost'] ?? null);
                                                                        $lab = is_object($d) ? ($d->labour_cost ?? 0) : ($d['labour_cost'] ?? 0);
                                                                        $mat = is_object($d) ? ($d->material_cost ?? 0) : ($d['material_cost'] ?? 0);
                                                                        return (float)($tc !== null ? $tc : ($lab + $mat));
                                                                    });
                                                                @endphp
                                                                <tfoot class="tfoot-dark">
                                                                    <tr>
                                                                        <td colspan="11" class="text-center font-weight-bold">Grand Total</td>
                                                                        <td colspan="2">{{ number_format($grandTotal, 2) }}</td>
                                                                    </tr>
                                                                </tfoot>
                                                            </table>
                                                        </div>
                                                    </div>

                                                    {{-- Tab Gate In PIC --}}
                                                    <div class="tab-pane fade" id="gateinpic" role="tabpanel" aria-labelledby="gateinpic-tab">
                                                        <div class="row">
                                                            @if (!empty($photos))
                                                                @foreach ($photos as $groupIndex => $photoGroup)
                                                                    @foreach ($photoGroup as $key => $photo)
                                                                        @php
                                                                            $modalId = "photoModal{$groupIndex}_" . $loop->iteration;
                                                                        @endphp
                                                                        <div class="col-md-4">
                                                                            <div class="card mb-4">
                                                                                <img src="{{ asset($photo) }}" class="card-img-top" alt="{{ $key }}"
                                                                                     data-toggle="modal" data-target="#{{ $modalId }}">
                                                                                <div class="card-body">
                                                                                    <h5 class="card-title">{{ $key }}</h5>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <!-- Modal -->
                                                                        <div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="{{ $modalId }}Label" aria-hidden="true">
                                                                            <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header">
                                                                                        <h5 class="modal-title" id="{{ $modalId }}Label">{{ $key }}</h5>
                                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                            <span aria-hidden="true">&times;</span>
                                                                                        </button>
                                                                                    </div>
                                                                                    <div class="modal-body">
                                                                                        <img src="{{ asset($photo) }}" class="img-fluid" alt="{{ $key }}">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                @endforeach
                                                            @else
                                                                <div class="col-12">
                                                                    <p class="text-center">No photos available.</p>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    {{-- Tab Take Action --}}
                                                    <div class="tab-pane fade" id="takeaction" role="tabpanel" aria-labelledby="takeaction-tab">
                                                        <div class="container text-center">
                                                            <div class="row mt-3">
                                                                <div class="col-md-6 d-flex justify-content-center align-items-center mb-3 mb-md-0">
                                                                    <form action="{{ route('eor.start', $eor->eor_code) }}" method="POST">
                                                                        @csrf
                                                                        <button type="submit" class="btn btn-primary btn-round"
                                                                                {{ $eor->date_started ? 'disabled' : '' }}>
                                                                            Mulai Kerja
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                                <div class="col-md-6 d-flex justify-content-center align-items-center">
                                                                    <form action="{{ route('eor.complete', $eor->eor_code) }}" method="POST">
                                                                        @csrf
                                                                        <button type="submit" class="btn btn-success btn-round"
                                                                                {{ $eor->date_completed ? 'disabled' : ($eor->date_started ? '' : 'disabled') }}>
                                                                            @if ($eor->date_completed)
                                                                                Selesai
                                                                            @else
                                                                                Selesaikan
                                                                            @endif
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> {{-- end tab-content --}}
                                            </div> {{-- end tabs wrapper --}}

                                            {{-- Actions --}}
                                            <div class="row mt-3">
                                                <div class="col-md-6 text-left">
                                                    <button type="submit" class="btn btn-primary">Update</button>
                                                    <a href="{{ route('eor.index') }}" class="btn btn-secondary">Cancel</a>
                                                </div>
                                                <div class="col-md-6 text-right">
                                                    <a class="btn btn-info mr-2" target="_blank"
                                                       href="{{ route('eor.cetak_eor', ['eor_code' => $eor->eor_code]) }}">
                                                        Print EOR <i class="fa fa-print"></i>
                                                    </a>
                                                </div>
                                            </div>

                                        </form>

                                    </div> {{-- card-body --}}
                                </div> {{-- card-content --}}
                            </div> {{-- card --}}
                        </div> {{-- col --}}
                    </div> {{-- row --}}
                </section>
            </div> {{-- content-body --}}
        </div> {{-- content-wrapper --}}
    </div> {{-- app-content --}}
@endsection
