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
                <h4 class="card-title text-white" id="basic-layout-form">Billing</h4>
              </div>
              <div class="card-content">
                <div class="card-body p-1">
                  <!-- Input pencarian nomor work order -->
                  <div class="row mb-1">
                    <div class="col-md-3">
                      <input type="text" class="form-control" id="searchWorkOrder" placeholder="Cari Nomor Work Order">
                    </div>
                    <div class="col-md-2">
                      <button class="btn btn-info" onclick="searchWorkOrder()">Cari</button>
                    </div>
                  </div>
                  <!-- Akhir input pencarian nomor work order -->
                  <!-- Input tersembunyi untuk menyimpan data -->
                  <input type="hidden" id="hidden_nomor_wo">
                  <input type="hidden" id="hidden_nama_customer">
                  <input type="hidden" id="hidden_no_container">
                  <input type="hidden" id="hidden_kegiatan">
                  <input type="hidden" id="hidden_total_tarif">
                  <div class="table-responsive">
                    <div id="data-table"></div>
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


<script>
  function searchWorkOrder() {
  console.log("searchWorkOrder function called");
  var searchKeyword = $('#searchWorkOrder').val();
  console.log("searchKeyword:", searchKeyword);
  $.ajax({
    url: '{{ route("billing.searchWorkOrder", ":searchKeyword") }}'.replace(':searchKeyword', searchKeyword),
    method: 'GET',
    success: function(response) {
      $('#data-table').html(response);
    },
    error: function(xhr, status, error) {
      console.error(error);
    }
  });
}
</script>


@endsection
