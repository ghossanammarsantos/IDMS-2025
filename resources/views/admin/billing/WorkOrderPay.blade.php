<form action="{{ route('billing.savePayment') }}" method="post">
    @csrf
    <input type="hidden" name="nomor_wo" value="{{ $searchResults[0]->nomor_wo }}">
    <input type="hidden" name="nama_customer" value="{{ $searchResults[0]->nama_customer }}">
    <!-- Tampilkan detail work order -->
    <div class="row">
        <div class="col-md-6">
            <h6 class="card-title mt-2">Nomor Work Order</h6>
            <input type="text" class="form-control" value="{{ $searchResults[0]->nomor_wo }}" readonly>
        </div>
        <div class="col-md-6">
            <h6 class="card-title mt-2">Nama Customer</h6>
            <input type="text" class="form-control" value="{{ $searchResults[0]->nama_customer }}" readonly>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-md-12">
           <h1>WO SUDAH DIBAYAR</h1>
        </div>
    </div>






