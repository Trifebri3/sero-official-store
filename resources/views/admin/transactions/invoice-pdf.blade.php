<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $transaction->reference_no }}</title>
    <style>
        @page { margin: 0; }
        body { font-family: 'Times New Roman', serif; color: #1a1a1a; margin: 0; padding: 0; }
        .bar { background: #1a1a1a; height: 8px; width: 100%; }
        .container { padding: 50px; }
        .header { text-align: center; margin-bottom: 40px; }
        .brand { font-size: 30px; letter-spacing: 6px; text-transform: uppercase; margin: 0; }
        .tagline { font-size: 10px; letter-spacing: 3px; color: #b4965a; text-transform: uppercase; margin-top: 5px; }
        .meta { width: 100%; margin-top: 30px; font-size: 12px; border-bottom: 1px solid #eee; padding-bottom: 20px; }
        .items { width: 100%; border-collapse: collapse; margin-top: 30px; }
        .items th { text-align: left; padding: 10px; border-bottom: 2px solid #1a1a1a; font-size: 11px; text-transform: uppercase; }
        .items td { padding: 15px 10px; border-bottom: 1px solid #f9f9f9; font-size: 12px; }
        .total-box { margin-top: 30px; width: 35%; margin-left: 65%; }
        .total-row { padding: 5px 0; font-size: 13px; }
        .grand-total { font-size: 18px; color: #b4965a; font-weight: bold; padding-top: 10px; border-top: 1px solid #1a1a1a; }
        .footer { position: absolute; bottom: 50px; width: 100%; text-align: center; font-size: 10px; color: #999; font-style: italic; }
    </style>
</head>
<body>
    <div class="bar"></div>
    <div class="container">
        <div class="header">
            @php $settings = $transaction->receipt_settings ?? []; @endphp
            <h1 class="brand">SERO OFFICIAL</h1>
            <div class="tagline">{{ $settings['header_note'] ?? 'Official Masterpiece Certificate' }}</div>
        </div>

        <table class="meta">
            <tr>
                <td>
                    <span style="color: #999; text-transform: uppercase; font-size: 9px;">Client Acquisition</span><br>
                    <strong>{{ $transaction->customer_info['name'] ?? 'Private Client' }}</strong>
                </td>
                <td style="text-align: right;">
                    <span style="color: #999; text-transform: uppercase; font-size: 9px;">Transaction ID</span><br>
                    <strong>#{{ $transaction->reference_no }}</strong><br>
                    {{ $transaction->created_at->format('M d, Y') }}
                </td>
            </tr>
        </table>

        <table class="items">
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align: center;">Qty</th>
                    <th style="text-align: right;">Valuation</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaction->items as $item)
                <tr>
                    <td><strong>{{ $item['name'] }}</strong></td>
                    <td style="text-align: center;">{{ $item['qty'] }}</td>
                    <td style="text-align: right;">Rp {{ number_format($item['qty'] * $item['price'], 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-box">
            <table width="100%">
                <tr>
                    <td>Subtotal</td>
                    <td style="text-align: right;">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="grand-total">Total Value</td>
                    <td class="grand-total" style="text-align: right;">Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <div class="footer">
            "{{ $settings['footer_note'] ?? 'Thank you for investing in excellence.' }}"
        </div>
    </div>
</body>
</html>
