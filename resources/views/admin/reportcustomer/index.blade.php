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
                                    <h4 class="card-title text-white" id="basic-layout-form">Report Stock by Customer</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body p-1">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-bordered">
                                                <thead class="thead-dark">
                                                <tr>
                                                    <th>Nama Customer</th>
                                                    <th>Jumlah Container</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($reportsCustomer as $data)
                                                    <tr>
                                                        <td>{{ $data->consignee }}</td>
                                                        <td>{{ $data->jumlah_container }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            </table>
                                        </div>
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



