<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Costing Produksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { background-color: #f4f6f9; font-size: 14px; }
        .card-header { background-color: #e0f7fa; font-weight: bold; }
        .table-custom thead { background-color: #f1f1f1; font-size: 12px; }
        .table-custom th { color: #555; text-transform: uppercase; }
        .input-tosca { background-color: #00e5ff; border: none; padding: 4px 8px; font-weight: bold; width: auto; display: inline-block; }
        .btn-set { border: 1px solid #d32f2f; color: #d32f2f; background: white; padding: 2px 10px; font-weight: bold; }
    </style>
</head>
<body>

<div class="container-fluid mt-4">
    <div class="card shadow-sm">
        <div class="card-header border-bottom-0">
            Bom List Costing Produksi
        </div>
        <div class="card-body p-0">
            <!-- Filter Section -->
            <div class="p-2" style="background-color: #e0f7fa; border-bottom: 2px solid #b2ebf2;">
                <span class="input-tosca">Formula :</span>
                <select class="form-select d-inline-block w-auto form-select-sm" disabled>
                    <option>{{ $order->formula->formula_name ?? 'PREMIX BROWNIES KUKUS' }}</option>
                </select>
                
                <span class="input-tosca ms-3">Qty Order :</span>
                <input type="text" class="form-control d-inline-block w-auto form-control-sm" value="{{ $order->qty_order ?? 1 }}" readonly>
                
                <button class="btn-set ms-3" onclick="alert('Tombol SET Web ditekan! ESP32 juga bisa dipakai langsung.')">SET</button>
            </div>

            <!-- Table Section -->
            <div class="table-responsive">
                <table class="table table-bordered table-custom table-sm mb-0 text-center align-middle" id="costingTable">
                    <thead>
                        <tr>
                            <th>ACTION</th>
                            <th>STATUS</th>
                            <th>KODE PRODUK</th>
                            <th>NAMA PRODUK</th>
                            <th>BOM KONVERSI</th>
                            <th>NETTO TARGET</th>
                            <th>NETTO PRODUKSI (Live)</th>
                            <th>STANDART COST</th>
                            <th>SUB PRICE</th>
                            <th>SUB COST PRICE</th>
                            <th>CREATE DATE</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($costings as $costing)
                        <tr id="row-{{ $costing->id }}" class="{{ $costing->status == 'Weighed' ? 'table-success' : '' }}">
                            <td>
                                <input type="checkbox" class="form-check-input">
                            </td>
                            <td>
                                @if($costing->status == 'Weighed')
                                    <span class="badge bg-success">✔</span>
                                @else
                                    <span class="badge bg-secondary">-</span>
                                @endif
                            </td>
                            <td>{{ $costing->bomItem->material->kode_produk }}</td>
                            <td class="text-start">{{ $costing->bomItem->material->nama_produk }}</td>
                            <td>{{ number_format($costing->bomItem->bom_konversi_qty, 2) }} {{ $costing->bomItem->bom_konversi_uom }}</td>
                            <td>
                                <input type="text" class="form-control form-control-sm text-center mx-auto" style="width: 80px;" value="{{ number_format($costing->netto_target, 3) }}" readonly>
                            </td>
                            <td class="fw-bold text-primary netto-produksi">
                                @if($costing->netto_produksi)
                                    {{ number_format($costing->netto_produksi, 3) }}
                                @else
                                    Menunggu Timbangan...
                                @endif
                            </td>
                            <td>{{ number_format($costing->price_bom, 2) }}</td>
                            <td>{{ number_format($costing->sub_price, 2) }}</td>
                            <td class="sub-cost-price">{{ number_format($costing->sub_cost_price, 2) }}</td>
                            <td>{{ $costing->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    // AJAX Polling sederhana untuk mengambil data live dari database/cache
    // Dalam produksi nyata, WebSocket (Laravel Reverb) sangat disarankan.
    setInterval(function() {
        $.ajax({
            url: "/api/costing-live/{{ $order->id }}",
            type: "GET",
            success: function(data) {
                // Update baris tabel secara live jika data berubah
                data.costings.forEach(function(item) {
                    var row = $('#row-' + item.id);
                    if (item.status === 'Weighed') {
                        row.addClass('table-success');
                        row.find('.badge').removeClass('bg-secondary').addClass('bg-success').text('✔');
                        row.find('.netto-produksi').text(parseFloat(item.netto_produksi).toFixed(3));
                        row.find('.sub-cost-price').text(parseFloat(item.sub_cost_price).toFixed(2));
                    }
                });
            }
        });
    }, 2000); // Cek setiap 2 detik
</script>

</body>
</html>
