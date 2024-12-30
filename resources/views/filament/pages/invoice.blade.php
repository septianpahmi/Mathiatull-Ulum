<!DOCTYPE html>
<html>

<head>
    <title>Invoice Pembayaran SPP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            line-height: 1.6;
        }

        .container {
            width: 80%;
            margin: 20px auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header img {
            width: 100px;
            margin-bottom: 10px;
        }

        .header h1 {
            font-size: 24px;
            margin: 0;
        }

        .header p {
            margin: 0;
            font-size: 12px;
            color: #555;
        }

        .details {
            margin-bottom: 30px;
        }

        .details th,
        .details td {
            text-align: left;
            padding: 3px 12px;
            font-size: 14px;
        }

        .details th {
            width: 150px;
            color: #555;
        }

        .details td {
            font-weight: bold;
        }

        .payment-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .payment-table th,
        .payment-table td {
            border: 1px solid #ddd;
            text-align: left;
            padding: 10px;
            font-size: 14px;
        }

        .payment-table th {
            background-color: #f8f8f8;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="{{ public_path('images/logo.png') }}" alt="Logo DTA" />
            <h1>Invoice Pembayaran SPP</h1>
            <p>Nama Sekolah</p>
        </div>

        <table class="details">
            <tr>
                <th>No. Pembayaran:</th>
                <td>{{ $pembayaran->no_pembayaran }}</td>
            </tr>
            <tr>
                <th>Nama:</th>
                <td>{{ $user->nama }}</td>
            </tr>
            <tr>
                <th>Tanggal Invoice:</th>
                <td>{{ now()->format('d-m-Y') }}</td>
            </tr>
        </table>

        <table class="payment-table">
            <thead>
                <tr>
                    <th>Deskripsi</th>
                    <th>Nominal</th>
                    <th>Tanggal Pembayaran</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Pembayaran SPP</td>
                    <td>Rp {{ number_format($pembayaran->nominal, 0, ',', '.') }}</td>
                    <td>{{ Carbon\Carbon::parse($pembayaran->tgl_pembayaran)->format('d-m-Y') }}</td>
                </tr>
            </tbody>
        </table>

        <p>Terima kasih atas pembayaran Anda. Mohon simpan invoice ini sebagai bukti pembayaran yang sah.</p>
    </div>
    <hr>
</body>

</html>
