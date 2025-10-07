@extends('admin.layouts.app', [
    'activePage' => 'Master',
])

@php
    use Illuminate\Support\Str;
@endphp

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-body">

                {{-- =======================
                    1) REPORT STOCK IN
                ======================== --}}
                <section id="basic-form-layouts">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-gradient-directional-info pl-2 pt-1 pb-1">
                                    <h4 class="card-title text-white" id="basic-layout-form">Report Stock In</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body p-1">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-bordered">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th scope="col">Ukuran Container</th>
                                                        <th scope="col">Jumlah Container</th>
                                                        <th scope="col">Jumlah TEUs</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($reportin as $data)
                                                        <tr>
                                                            <td>{{ $data->size_type ?? '—' }}</td>
                                                            <td>{{ number_format($data->total ?? 0) }}</td>
                                                            <td>{{ number_format($data->teus ?? 0) }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="3" class="text-center">Tidak ada data.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                                @if ($reportin && $reportin->count())
                                                    <tfoot>
                                                        <tr class="font-weight-bold">
                                                            <td>Total</td>
                                                            <td>{{ number_format($reportin->sum('total')) }}</td>
                                                            <td>{{ number_format($reportin->sum('teus')) }}</td>
                                                        </tr>
                                                    </tfoot>
                                                @endif
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- =======================
                    2) REPORT STOCK BY CUSTOMER
                ======================== --}}
                <section id="basic-form-layouts">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-gradient-directional-info pl-2 pt-1 pb-1">
                                    <h4 class="card-title text-white" id="basic-layout-form">Report Stock by Customer</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body p-1">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-bordered">
                                                <thead class="thead-dark">
                                                    <tr class="text-center">
                                                        <th rowspan="2" scope="col" class="align-middle">Nama Customer
                                                        </th>
                                                        <th colspan="2" scope="colgroup">Jumlah Container</th>
                                                        <th rowspan="2" scope="col" class="align-middle">Jumlah TEUs
                                                        </th>
                                                    </tr>
                                                    <tr class="text-center">
                                                        <th scope="col">20GP</th>
                                                        <th scope="col">40HC</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($reportsCustomerTeus as $data)
                                                        <tr>
                                                            <td>{{ $data->consignee ?: '—' }}</td>
                                                            <td>{{ number_format($data->jumlah_20gp ?? 0) }}</td>
                                                            <td>{{ number_format($data->jumlah_40hc ?? 0) }}</td>
                                                            <td>{{ number_format($data->teus ?? 0) }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="4" class="text-center">Tidak ada data.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                                @if ($reportsCustomerTeus && $reportsCustomerTeus->count())
                                                    <tfoot>
                                                        <tr class="font-weight-bold">
                                                            <td>Total</td>
                                                            <td>{{ number_format($reportsCustomerTeus->sum('jumlah_20gp')) }}
                                                            </td>
                                                            <td>{{ number_format($reportsCustomerTeus->sum('jumlah_40hc')) }}
                                                            </td>
                                                            <td>{{ number_format($reportsCustomerTeus->sum('teus')) }}</td>
                                                        </tr>
                                                    </tfoot>
                                                @endif
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- =======================
                    3) CONTAINER MASUK PER HARI (DETAIL SURVEY IN)
                ======================== --}}
                <section id="basic-form-layouts">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-gradient-directional-info pl-2 pt-1 pb-1">
                                    <h4 class="card-title text-white" id="basic-layout-form">
                                        Container Masuk per Hari (Detail Survey In)
                                    </h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body p-1">

                                        {{-- Tombol Download Semua --}}
                                        <div class="d-flex justify-content-end mb-2">
                                            <a href="{{ route('reportin.export', ['all' => 1]) }}"
                                                class="btn btn-success btn-sm">
                                                <i class="ft-download"></i> Download Excel (Semua Tanggal)
                                            </a>
                                        </div>

                                        @if (isset($surveyInPerDay) && $surveyInPerDay->isNotEmpty())
                                            @foreach ($surveyInPerDay as $tanggal => $rows)
                                                <div class="mb-2">
                                                    <h5 class="mb-1 d-flex align-items-center justify-content-between">
                                                        <span>
                                                            Tanggal:
                                                            @if ($tanggal !== 'Tanpa Tanggal')
                                                                {{ \Carbon\Carbon::parse($tanggal)->format('d M Y') }}
                                                            @else
                                                                Tanpa Tanggal
                                                            @endif
                                                            <small class="text-muted"> ({{ $rows->count() }}
                                                                container)</small>
                                                        </span>

                                                        {{-- Tombol Download per Tanggal --}}
                                                        @if ($tanggal !== 'Tanpa Tanggal')
                                                            <a href="{{ route('reportin.export', ['date' => $tanggal]) }}"
                                                                class="btn btn-outline-success btn-sm">
                                                                <i class="ft-download"></i> Download Excel
                                                                ({{ \Carbon\Carbon::parse($tanggal)->format('d M Y') }})
                                                            </a>
                                                        @endif
                                                    </h5>

                                                    <div class="table-responsive">
                                                        <table class="table table-hover table-bordered">
                                                            <thead class="thead-dark">
                                                                <tr>
                                                                    @foreach ($surveyinColumnsLower as $col)
                                                                        <th scope="col">
                                                                            {{ strtoupper(str_replace('_', ' ', $col)) }}
                                                                        </th>
                                                                    @endforeach
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @forelse ($rows as $r)
                                                                    <tr>
                                                                        @foreach ($surveyinColumnsLower as $col)
                                                                            <td>
                                                                                @php $val = $r->{$col} ?? null; @endphp

                                                                                {{-- Format tanggal/jam bila memungkinkan --}}
                                                                                @if (Str::contains($col, ['time', 'date', 'tgl', 'waktu', 'jam']))
                                                                                    @php
                                                                                        $display = '—';
                                                                                        try {
                                                                                            if ($val) {
                                                                                                $display = \Carbon\Carbon::parse(
                                                                                                    $val,
                                                                                                )->format('d-m-Y H:i');
                                                                                            }
                                                                                        } catch (\Throwable $e) {
                                                                                            $display = $val ?? '—';
                                                                                        }
                                                                                    @endphp
                                                                                    {{ $display }}
                                                                                    {{-- Angka --}}
                                                                                @elseif (Str::contains($col, ['payload', 'tare', 'maxgross', 'sizze', 'status_gatein', 'status_gateout']))
                                                                                    {{ is_numeric($val) ? number_format($val) : $val ?? '—' }}
                                                                                    {{-- Default --}}
                                                                                @else
                                                                                    {{ $val ?? '—' }}
                                                                                @endif
                                                                            </td>
                                                                        @endforeach
                                                                    </tr>
                                                                @empty
                                                                    <tr>
                                                                        <td colspan="{{ count($surveyinColumnsLower) }}"
                                                                            class="text-center">Tidak ada data untuk tanggal
                                                                            ini.</td>
                                                                    </tr>
                                                                @endforelse
                                                            </tbody>
                                                            <tfoot>
                                                                <tr class="font-weight-bold">
                                                                    <td colspan="{{ count($surveyinColumnsLower) }}"
                                                                        class="text-right">
                                                                        Total container: {{ $rows->count() }}
                                                                    </td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="alert alert-info m-1">
                                                Belum ada data container masuk (survey in) yang memenuhi kriteria.
                                            </div>
                                        @endif

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            // Aktifkan menu sidebar
            $('#sidebar-menu .nav-item').removeClass('active');
            $('#menu-master').addClass('active');
            $('#menu-reportin').addClass('active');
        });
    </script>
