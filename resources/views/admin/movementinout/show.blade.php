@extends('admin.layouts.app', [
    'activePage' => 'Master',
])

@section('content')

{{-- ===== Modal Viewer Styles (sekali saja di halaman ini) ===== --}}
<style>
  /* Modal image/PDF viewer */
  .modal-content.viewer {
    background: #aa9a9aff; /* dark bg biar foto lebih jelas */
    color: #fff;
    border: none;
  }
  .modal-content.viewer .modal-header {
    border: 0;
  }
  .viewer-wrap {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 60vh;
    background: #aa9a9aff;
  }
  .viewer-wrap img,
  .viewer-wrap embed,
  .viewer-wrap iframe {
    max-width: 100%;
    max-height: 85vh; /* batasi tinggi agar nyaman */
    width: auto;
    height: auto;
    object-fit: contain;
    display: block;
  }
  /* Biar modal lebar mendekati penuh layar */
  .modal-dialog.modal-xl {
    max-width: 95vw;
  }
  /* Cursor pointer untuk thumbnail */
  .card-img-top { cursor: pointer; }
</style>

<div class="container mt-4">
    <div class="row">
        <ul class="nav nav-tabs w-100 d-flex justify-content-center" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="gateintime-tab" data-toggle="tab" href="#gateintime" role="tab" aria-controls="gateintime" aria-selected="true">Gate In Time</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="surveyintime-tab" data-toggle="tab" href="#surveyintime" role="tab" aria-controls="surveyintime" aria-selected="false">Survey In Time & Photos</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="suratjalan-tab" data-toggle="tab" href="#suratjalan" role="tab" aria-controls="suratjalan" aria-selected="false">Surat Jalan</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="surveyouttime-tab" data-toggle="tab" href="#surveyouttime" role="tab" aria-controls="surveyouttime" aria-selected="false">Survey Out Time & Photos</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="gateouttime-tab" data-toggle="tab" href="#gateouttime" role="tab" aria-controls="gateouttime" aria-selected="false">Gate Out Time</a>
            </li>
        </ul>
    </div>

    <div class="tab-content" id="myTabContent">
        <!-- Gate In Time Tab -->
        <div class="tab-pane fade show active" id="gateintime" role="tabpanel" aria-labelledby="gateintime-tab">
            <div class="mt-3">
                <h5>Gate In Time</h5>
                <p>{{ $data->gatein_time ?? 'No data available' }}</p>
            </div>
        </div>

        <!-- Survey In Time & Photos Tab -->
        <div class="tab-pane fade" id="surveyintime" role="tabpanel" aria-labelledby="surveyintime-tab">
            <div class="mt-3">
                <h5>Survey In Time</h5>
                <p>{{ $data->surveyin_time ?? 'No data available' }}</p>

                <h5 class="mt-4">Survey In Photos</h5>
                <div class="row">
                    @if (!empty($surveyinPhotos))
                        @foreach ($surveyinPhotos as $label => $paths)
                            @php
                                $paths = is_array($paths) ? $paths : [$paths];
                            @endphp

                            @foreach ($paths as $idx => $photo)
                                @php
                                    $safeLabel = preg_replace('/[^a-z0-9_]/i', '_', (string)$label);
                                    $modalId   = "photoModalSurveyIn_{$safeLabel}_{$idx}";
                                    $src       = asset($photo); // jika pakai Storage::disk('public'): asset('storage/'.$photo)
                                @endphp

                                <div class="col-md-4 mb-4">
                                    <div class="card h-100">
                                        <img src="{{ $src }}" class="card-img-top" alt="{{ $label }}"
                                             data-toggle="modal" data-target="#{{ $modalId }}">
                                        <div class="card-body">
                                            <h5 class="card-title mb-0">{{ $label }}</h5>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal (Survey In) -->
                                <div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog"
                                     aria-labelledby="{{ $modalId }}Label" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                                        <div class="modal-content viewer">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="{{ $modalId }}Label">{{ $label }}</h5>
                                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body p-0">
                                                <div class="viewer-wrap">
                                                    <img src="{{ $src }}" alt="{{ $label }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endforeach
                    @else
                        <div class="col-12">
                            <p>No photos available.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Surat Jalan Tab -->
        <div class="tab-pane fade" id="suratjalan" role="tabpanel" aria-labelledby="suratjalan-tab">
            <div class="mt-3">
                <h5>Surat Jalan</h5>

                @php
                    // $data->foto_surat_jalan bisa berupa path tunggal (string) atau JSON array of paths.
                    $suratJalanList = [];
                    if (!empty($data->foto_surat_jalan)) {
                        $decoded = json_decode($data->foto_surat_jalan, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                            $suratJalanList = $decoded; // banyak file (JSON)
                        } else {
                            $suratJalanList = [$data->foto_surat_jalan]; // satu file (string)
                        }
                    }
                @endphp

                <div class="row">
                    @forelse($suratJalanList as $idx => $path)
                        @php
                            $modalId = 'sjModal' . $idx;
                            $ext     = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                            // Jika file disimpan via Storage::disk('public'), path biasanya "storage/xxx/yyy.ext"
                            // Jika simpan manual ke public_path('surat_jalan_photo'), path "surat_jalan_photo/yyy.ext"
                            $src     = asset($path); // sesuaikan jika perlu: asset('storage/'.$path)
                        @endphp

                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                @if($ext === 'pdf')
                                    <div class="p-4 text-center">
                                        <i class="fa fa-file-pdf-o fa-3x mb-2"></i>
                                        <p class="mb-2">File PDF Surat Jalan</p>
                                        <a class="btn btn-outline-primary btn-sm" href="{{ $src }}" target="_blank">Buka PDF</a>
                                        {{-- Jika ingin preview di modal juga, klik kartu untuk buka modal --}}
                                        <div class="mt-2">
                                            <button class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#{{ $modalId }}">Preview</button>
                                        </div>
                                    </div>
                                @else
                                    <img src="{{ $src }}" class="card-img-top" alt="Surat Jalan {{ $idx + 1 }}"
                                         data-toggle="modal" data-target="#{{ $modalId }}">
                                @endif
                                <div class="card-body">
                                    <h5 class="card-title mb-0">Surat Jalan {{ $idx + 1 }}</h5>
                                </div>
                            </div>
                        </div>

                        {{-- Modal untuk gambar atau PDF --}}
                        <div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog"
                             aria-labelledby="{{ $modalId }}Label" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                                <div class="modal-content viewer">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="{{ $modalId }}Label">
                                            Surat Jalan {{ $idx + 1 }}{{ $ext === 'pdf' ? ' (PDF)' : '' }}
                                        </h5>
                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body p-0">
                                        <div class="viewer-wrap">
                                            @if($ext === 'pdf')
                                                <embed src="{{ $src }}" type="application/pdf">
                                            @else
                                                <img src="{{ $src }}" alt="Surat Jalan {{ $idx + 1 }}">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <p>No surat jalan photo available.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Survey Out Time & Photos Tab -->
        <div class="tab-pane fade" id="surveyouttime" role="tabpanel" aria-labelledby="surveyouttime-tab">
            <div class="mt-3">
                <h5>Survey Out Time</h5>
                <p>{{ $data->surveyout_time ?? 'No data available' }}</p>

                <h5 class="mt-4">Survey Out Photos</h5>
                <div class="row">
                    @if (!empty($surveyoutPhotos))
                        @foreach ($surveyoutPhotos as $label => $paths)
                            @php
                                $paths = is_array($paths) ? $paths : [$paths];
                            @endphp

                            @foreach ($paths as $idx => $photo)
                                @php
                                    $safeLabel = preg_replace('/[^a-z0-9_]/i', '_', (string)$label);
                                    $modalId   = "photoModalSurveyOut_{$safeLabel}_{$idx}";
                                    $src       = asset($photo);
                                @endphp

                                <div class="col-md-4 mb-4">
                                    <div class="card h-100">
                                        <img src="{{ $src }}" class="card-img-top" alt="{{ $label }}"
                                             data-toggle="modal" data-target="#{{ $modalId }}">
                                        <div class="card-body">
                                            <h5 class="card-title mb-0">{{ $label }}</h5>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal (Survey Out) -->
                                <div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog"
                                     aria-labelledby="{{ $modalId }}Label" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                                        <div class="modal-content viewer">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="{{ $modalId }}Label">{{ $label }}</h5>
                                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body p-0">
                                                <div class="viewer-wrap">
                                                    <img src="{{ $src }}" alt="{{ $label }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endforeach
                    @else
                        <div class="col-12">
                            <p>No photos available.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Gate Out Time Tab -->
        <div class="tab-pane fade" id="gateouttime" role="tabpanel" aria-labelledby="gateouttime-tab">
            <div class="mt-3">
                <h5>Gate Out Time</h5>
                <p>{{ $data->gateout_time ?? 'No data available' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
