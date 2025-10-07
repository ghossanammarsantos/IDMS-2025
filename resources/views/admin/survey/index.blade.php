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
                                    <h4 class="card-title text-white" id="basic-layout-form">Survey</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body p-1">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <a href="{{ route('survey.create') }}"
                                                    class="btn btn-dark btn-min-width mr-1 mb-1">
                                                    <i class="fa fa-plus"></i> Add New
                                                </a>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-hover table-bordered">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th>No. Container</th>
                                                        <th>Jenis Container</th>
                                                        <th>Ukuran Container</th>
                                                        <th>Waktu Gate In</th>
                                                        <th>PIC Gate In</th>
                                                        <th>Status Container</th>
                                                        <th>Grade Container</th>
                                                        <th>kegiatan</th>
                                                        <th>Waktu Survey</th>
                                                        <th>PIC Survey</th>
                                                        <th>Status WO</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($survey_list as $data)
                                                        <tr>
                                                            <td>{{ $data->no_container }}</td>
                                                            <td>{{ isset($data->jenis_container) ? $data->jenis_container : '-' }}
                                                            </td>
                                                            <td>{{ isset($data->ukuran_container) ? $data->ukuran_container : '-' }}
                                                            </td>
                                                            <td>{{ isset($data->gatein_time) ? $data->gatein_time : '-' }}
                                                            </td>
                                                            <td>{{ isset($data->pic_gatein) ? $data->pic_gatein : '-' }}
                                                            </td>
                                                            <td>{{ $data->status_container }}</td>
                                                            <td>{{ $data->grade_container }}</td>
                                                            <td>{{ $data->kegiatan }}</td>
                                                            <td>{{ $data->survey_time }}</td>
                                                            <td>{{ $data->pic }}</td>
                                                            <td>{{ $data->status_wo }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <ul class="pagination pagination-separate pagination-curved firstLast2-links">
                                            <li class="page-item first disabled"><a href="#"
                                                    class="page-link bg-light">First</a></li>
                                            <li class="page-item prev disabled"><a href="#"
                                                    class="page-link bg-light">Prev</a></li>
                                            <li class="page-item active"><a href="#" class="page-link">1</a></li>
                                            <li class="page-item"><a href="#" class="page-link">2</a></li>
                                            <li class="page-item"><a href="#" class="page-link">3</a></li>
                                            <li class="page-item"><a href="#" class="page-link">4</a></li>
                                            <li class="page-item next"><a href="#" class="page-link">Next</a></li>
                                            <li class="page-item last"><a href="#" class="page-link">Last</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
    </div>
    </div>
@endsection
