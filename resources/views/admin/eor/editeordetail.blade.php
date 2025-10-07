@extends('admin.layouts.app', ['activePage' => 'Master'])

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-body">
                <section id="basic-form-layouts">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-gradient-directional-info pl-2 pt-1 pb-1">
                                    <h4 class="card-title text-white" id="basic-layout-form">Edit Detail EOR</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body p-1">
                                        <form action="{{ route('eor.updateeordetail', $eor_detail->eor_code) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="eor_code">Kode EOR</label>
                                                        <input type="text" id="eor_code" name="eor_code" class="form-control" value="{{ $eor_detail->eor_code }}" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="kode_survey">Kode Survey</label>
                                                        <input type="text" id="kode_survey" name="kode_survey" class="form-control" value="{{ $eor_detail->kode_survey }}" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="no_container">No. Container</label>
                                                        <input type="text" id="no_container" name="no_container" class="form-control" value="{{ $eor_detail->no_container }}" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="componentInput" class="red-label">COMPONENT</label>
                                                        <select class="form-control select2" name="component" id="componentInputcmp" style="width:100%;">
                                                            <option value="">{{ $eor_detail->component }}</option>
                                                            @foreach ($components as $itemcmp)
                                                                <option value="{{ $itemcmp->cedex_code }}">
                                                                    {{ $itemcmp->cedex_code }} - {{ $itemcmp->deskripsi }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="locationInput" class="red-label">LOCATION</label>
                                                        <input type="text" class="form-control" name="location" id="locationInput" value="{{ $eor_detail->location }}" style="width:100%;">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="damageInput" class="red-label">DAMAGE</label>
                                                        <select class="form-control select2" name="damage" id="damageInput" style="width:100%;">
                                                            <option value="">{{ $eor_detail->damage }}</option>
                                                            @foreach ($damages as $itemdmg)
                                                                <option value="{{ $itemdmg->cedex_code }}">
                                                                    {{ $itemdmg->cedex_code }} - {{ $itemdmg->deskripsi }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="repairInput" class="red-label">REPAIR</label>
                                                        <select class="form-control select2" name="repair" id="repairInput" style="width:100%;">
                                                            <option value="">{{ $eor_detail->repair }}</option>
                                                            @foreach ($repairs as $itemrpr)
                                                                <option value="{{ $itemrpr->cedex_code }}">
                                                                    {{ $itemrpr->cedex_code }} - {{ $itemrpr->deskripsi }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="sizeInput" class="red-label">SIZE</label>
                                                        <input type="text" class="form-control" name="size_repair" id="sizeInput" value="{{ $eor_detail->size_repair }}" style="width:100%;">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="qtyInput" class="red-label">QTY</label>
                                                        <input type="number" class="form-control" name="qty" id="qtyInput" value="{{ $eor_detail->qty }}" style="width:100%;">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="manhourInput" class="red-label">MANHOUR</label>
                                                        <input type="text" class="form-control" name="manhour" id="manhourInput" value="{{ $eor_detail->manhour }}" style="width:100%;">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="whInput" class="red-label">W/H</label>
                                                        <input type="text" class="form-control" name="wh" id="whInput" value="{{ $eor_detail->wh }}" style="width:100%;">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="labourCostInput" class="red-label">Labour Cost</label>
                                                        <input type="number" class="form-control" name="labour_cost" id="labourCostInput" value="{{ $eor_detail->labour_cost }}" style="width:100%;">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="materialCostInput" class="red-label">Material Cost</label>
                                                        <input type="number" class="form-control" name="material_cost" id="materialCostInput" value="{{ $eor_detail->material_cost }}" style="width:100%;">
                                                    </div>
                                                </div>
                                            </div>
                            
                                            <div class="row mt-3">
                                                <div class="col-md-6 text-left">
                                                    <button type="submit" class="btn btn-primary">Update</button>
                                                    <a href="{{ route('eor.edit', ['eor_code' => $eor_detail->eor_code]) }}" class="btn btn-secondary">Cancel</a>
                                                </div>
                                            </div>
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
