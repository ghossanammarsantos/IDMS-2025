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
                                    <h4 class="card-title text-white" id="basic-layout-form">Add New Survey</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body p-1">
                                        <!-- Form untuk menambah survey -->
                                        <form id="addSurveyForm" method="post" action="{{ route('survey.store_detail') }}">
                                            @csrf
                                            <div class="form-group">
                                                <label for="noContainerSelect">Pilih No. Container</label>
                                                <select class="form-control" id="noContainerSelect" name="no_container">
                                                    <option value="">--- Pilih No Container ---
                                                        </option>
                                                    @foreach ($gate_in as $data)
                                                        <option value="{{ $data->no_container }}">{{ $data->no_container }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="statusRadio">Status Container</label><br>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" id="statusRadio"
                                                        name="status_container" value="AV">
                                                    <label class="form-check-label" for="statusRadio">AV</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" id="statusRadio2"
                                                        name="status_container" value="DM">
                                                    <label class="form-check-label" for="statusRadio2">DM</label>
                                                </div>
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
                                            </div>
                                            <div class="form-group">
                                                <label for="noContainerSelect">Pilih Kegiatan Container</label>
                                                <select class="select2 form-control"
                                                    multiple="multiple" name="kegiatan[]"
                                                    id="containerSelect"
                                                    style="width:100%;">
                                                    @foreach ($tarif_list as $data)
                                                        <option value="{{ $data->nama_jasa }}">{{ $data->nama_jasa }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="noContainerSelect">PIC Survey</label>
                                                <select class="form-control" id="noContainerSelect" name="pic">
                                                    <option value="">--- Pilih PIC ---</option>
                                                    <option value="Suprapto">Suprapto</option>
                                                    <option value="Samsul">Samsul</option>
                                                </select>
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
@endsection
