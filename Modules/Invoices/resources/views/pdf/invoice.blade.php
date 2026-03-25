<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->number }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; color: #1f2937; font-size: 12px; }
        .container { width: 100%; }
        .row { width: 100%; clear: both; }
        .left { float: left; width: 58%; }
        .right { float: right; width: 40%; text-align: right; }
        h1 { margin: 0 0 8px; font-size: 22px; letter-spacing: 0.04em; }
        .muted { color: #6b7280; }
        .box { border: 1px solid #e5e7eb; padding: 10px; border-radius: 6px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th { background: #f3f4f6; text-align: left; font-size: 11px; text-transform: uppercase; color: #6b7280; padding: 8px; }
        td { border-bottom: 1px solid #e5e7eb; padding: 8px; vertical-align: top; }
        .text-right { text-align: right; }
        .totals { margin-top: 14px; width: 45%; float: right; }
        .totals td { border: none; padding: 4px 0; }
        .totals .grand td { border-top: 1px solid #d1d5db; font-size: 14px; font-weight: bold; padding-top: 8px; }
        .footer { position: fixed; bottom: 24px; left: 0; right: 0; text-align: center; color: #6b7280; font-size: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="left">
                <h1>INVOICE</h1>
                <div>{{ data_get($company, 'name', config('crm.app_name')) }}</div>
                <div class="muted">{{ data_get($company, 'address', 'Address not configured') }}</div>
                <div class="muted">{{ data_get($company, 'phone', '') }} {{ data_get($company, 'email', '') }}</div>
                <div class="muted">VAT: {{ data_get($company, 'vat_number', 'N/A') }}</div>
            </div>
            <div class="right">
                <div><strong>{{ $invoice->number }}</strong></div>
                <div class="muted">Issue: {{ $invoice->issue_date?->toDateString() }}</div>
                <div class="muted">Due: {{ $invoice->due_date?->toDateString() }}</div>
                <div class="muted">Status: {{ $invoice->status }}</div>
            </div>
        </div>

        <div style="clear: both; margin-top: 18px;" class="box">
            <strong>Bill To</strong><br>
            {{ $invoice->account?->name ?? '—' }}<br>
            {{ trim(($invoice->contact?->first_name ?? '').' '.($invoice->contact?->last_name ?? '')) ?: '—' }}<br>
            {{ $invoice->contact?->email ?? '' }}
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width:4%;">#</th>
                    <th>Description</th>
                    <th class="text-right" style="width:10%;">Qty</th>
                    <th class="text-right" style="width:12%;">Unit Price</th>
                    <th class="text-right" style="width:10%;">Disc%</th>
                    <th class="text-right" style="width:10%;">Tax%</th>
                    <th class="text-right" style="width:12%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->lineItems as $index => $lineItem)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $lineItem->name }}</td>
                        <td class="text-right">{{ number_format((float) $lineItem->quantity, 2) }}</td>
                        <td class="text-right">{{ number_format((float) $lineItem->unit_price, 2) }}</td>
                        <td class="text-right">{{ number_format((float) $lineItem->discount_percent, 2) }}</td>
                        <td class="text-right">{{ number_format((float) $lineItem->tax_rate, 2) }}</td>
                        <td class="text-right">{{ number_format((float) $lineItem->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="totals">
            <tr>
                <td>Subtotal</td>
                <td class="text-right">{{ number_format((float) $invoice->subtotal, 2) }}</td>
            </tr>
            <tr>
                <td>Discount</td>
                <td class="text-right">-{{ number_format((float) $invoice->discount_amount, 2) }}</td>
            </tr>
            <tr>
                <td>Tax</td>
                <td class="text-right">{{ number_format((float) $invoice->tax_amount, 2) }}</td>
            </tr>
            <tr>
                <td>Amount Paid</td>
                <td class="text-right">{{ number_format((float) $invoice->amount_paid, 2) }}</td>
            </tr>
            <tr class="grand">
                <td>Balance Due</td>
                <td class="text-right">{{ number_format($invoice->balance_due, 2) }} {{ $invoice->currency }}</td>
            </tr>
        </table>

        <div style="clear: both; margin-top: 24px;" class="box">
            <strong>Payment Instructions</strong><br>
            <span class="muted">{{ $invoice->notes ?: 'Please pay by due date.' }}</span><br><br>
            <strong>Bank details</strong><br>
            <span class="muted">{{ data_get($bankDetails, 'account_name', 'N/A') }}</span><br>
            <span class="muted">{{ data_get($bankDetails, 'iban', 'N/A') }}</span><br>
            <span class="muted">{{ data_get($bankDetails, 'swift', 'N/A') }}</span>
        </div>
    </div>

    <div class="footer">
        Thank you for your business. | Page 1
    </div>
</body>
</html>
