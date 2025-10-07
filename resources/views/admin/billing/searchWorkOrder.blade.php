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
    <!-- Tabel Utama -->
    <div class="row mt-3">
        <div class="col-md-12">
            <table class="table table-bordered">
                <thead>
                    <tr class="bg-dark text-white">
                        <th class="text-center">No</th>
                        <th>Nomor Container</th>
                        <th>No BL/DO</th>
                        <th>Kegiatan</th>
                        <th>Status</th>
                        <th colspan="3">Tarif</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalTarif = 0;
                        $jml_cont = 1;
                        $nomor_urut = 1; // Inisialisasi nomor urut
                    @endphp
                    @foreach($searchResults as $row)
                        <tr>
                            <td>{{ $nomor_urut++ }}</td>
                            <td>{{ $row->no_container }}</td>
                            <td>{{ $row->no_bldo }}</td>
                            <td>{{ $row->kegiatan }}</td>
                            <td>{{ $row->status_container }}</td>
                            <td colspan="3">{{ 'Rp '.number_format($row->tarif, 0, ',', '.') }}</td>
                        </tr>
                        @php
                            $totalTarif += $row->tarif;
                            $jml_cont++;
                        @endphp

                        @if($row->status_container === 'DM' && count($row->dm_details) > 0)
                            @foreach($row->dm_details as $dm)
                                <tr>
                                    <td colspan="3"></td> 
                                    <td>({{ $dm->component }} {{ $dm->location }} {{ $dm->damage }} {{ $dm->repair }} {{ $dm->size_repair }})</td>
                                    <td>DM</td>
                                    <td colspan="3">{{ 'Rp '.number_format($dm->total_cost, 0, ',', '.') }}</td>
                                </tr>
                                @php
                                    $totalTarif += $dm->total_cost; // Menambahkan total cost DM ke totalTarif
                                @endphp
                            @endforeach
                        @endif
                    @endforeach
                    <tr>
                        <td><strong>Jumlah Container : {{ $jml_cont-1 }}</strong></td>
                        <input type="hidden" name="jumlah_container" value="{{ $jml_cont-1 }}">
                        <td colspan="6"><strong>Total Tarif</strong></td>
                        <td>{{ 'Rp '.number_format($totalTarif, 0, ',', '.') }}</td>
                    </tr>
                    <!-- Baris untuk diskon dan total bayar -->
                    <tr>
                        <td colspan="6"><strong>Diskon (%)</strong></td>
                        <td colspan="2">
                            <input type="number" class="form-control" id="diskon" name="diskon" placeholder="Masukkan diskon dalam persen" min="0" max="100" step="any">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6"><strong>Jumlah Diskon</strong></td>
                        <td colspan="2">
                        <input type="text" class="form-control" id="jml_diskon" name="jml_diskon" readonly>
                        <input type="hidden" id="hidden_jml_diskon" name="hidden_jml_diskon">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6"><strong>Total Yang Harus di Bayar</strong></td>
                        <td colspan="2">
                            <input type="text" class="form-control" id="total_bayar" name="total_bayar" readonly>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Input pembayaran -->
    <div class="row mt-3">
        <div class="col-md-6">
            <label for="total_payment">Total Pembayaran</label>
            <input type="text" class="form-control" id="total_payment" name="total_payment" placeholder="Masukkan jumlah pembayaran" type-currency="IDR" required>
        </div>
        <div class="col-md-6">
            <label for="payment_method">Metode Pembayaran</label>
            <select class="form-control" id="payment_method" name="payment_method" required>
                <option value="TRANSFER">Transfer</option>
                <option value="DEPOSIT">Deposit</option>
                <option value="CASH">Cash</option>
            </select>
        </div>
    </div>

    <div class="row mt-3">
        
        <div class="col-md-6">
            <label for="sisa_bayar">Sisa yang harus di Bayar</label>
            <input type="text" class="form-control" id="sisa_bayar" name="sisa_bayar" readonly>
            <input type="hidden" id="hidden_sisa_bayar" name="hidden_sisa_bayar">
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-12">
            <button type="submit" class="btn btn-primary">Bayar</button>
        </div>
    </div>

    <script>
var totalTarif = {{ $totalTarif }};

function formatCurrency(value) {
    return value.toString().replace(/\D/g, "")
                           .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function parseCurrency(value) {
    return parseFloat(value.replace(/,/g, '')) || 0; // Ensure we return 0 if parsing fails
}

function hitungTotalBayar() {
    var pembayaranElement = document.getElementById('total_payment');
    var pembayaran = parseCurrency(pembayaranElement.value);
    var diskon = parseFloat(document.getElementById('diskon').value) || 0; // Ensure diskon is a valid number

    if (diskon > 100) {
        alert('Diskon tidak boleh lebih dari 100%.');
        document.getElementById('diskon').value = '';
        diskon = 0;
    }

    // Hitung total bayar setelah diskon
    var jumlahBayarSetelahDiskon = totalTarif - (totalTarif * diskon / 100);

    // Tampilkan jumlah bayar setelah diskon
    document.getElementById('total_bayar').value = 'Rp ' + ((jumlahBayarSetelahDiskon) ? ( (jumlahBayarSetelahDiskon < 0) ? "0" : jumlahBayarSetelahDiskon.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") ): "0");

    // Hitung dan tampilkan jumlah diskon
    var jmlDiskon = totalTarif - jumlahBayarSetelahDiskon;
    document.getElementById('jml_diskon').value = 'Rp ' + ((jmlDiskon) ? ( (jmlDiskon < 0) ? "0" : jmlDiskon.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") ): "0");
    // Simpan nilai sisa bayar ke dalam input tersembunyi
    document.getElementById('hidden_jml_diskon').value = jmlDiskon;

    // Hitung dan tampilkan sisa bayar
    var sisaBayar = jumlahBayarSetelahDiskon - pembayaran;
    if (sisaBayar < 0) {
        var kelebihanBayar = Math.abs(sisaBayar);
        document.getElementById('sisa_bayar').value = "pembayaran berlebih Rp " + kelebihanBayar.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + "";
        document.getElementById('hidden_sisa_bayar').value = sisaBayar;
    } else {
        document.getElementById('sisa_bayar').value = "Rp. " + ((sisaBayar) ? ( (sisaBayar < 0) ? "0" : sisaBayar.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") ): "0");
        document.getElementById('hidden_sisa_bayar').value = sisaBayar;
    }
}

// Add an event listener to elements with type-currency="IDR"
document.querySelectorAll('[type-currency="IDR"]').forEach(function(input) {
    input.addEventListener('input', function(event) {
        var value = event.target.value.replace(/,/g, '');
        if (!isNaN(value) && value.trim() !== '') {
            event.target.value = formatCurrency(value);
        }
        hitungTotalBayar();
    });
});

document.getElementById('diskon').addEventListener('input', hitungTotalBayar);
</script>



</form>
