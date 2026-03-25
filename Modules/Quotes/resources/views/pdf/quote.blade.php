@php
    $companyData = is_array($company ?? null) ? $company : [];
@endphp
<div style="font-family: DejaVu Sans, Arial, sans-serif; color: #111827; font-size: 12px; line-height: 1.5;">
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 24px;">
        <tr>
            <td style="vertical-align: top;">
                <h1 style="margin: 0 0 8px 0; font-size: 22px; color: #0f172a;">{{ $companyData['name'] ?? config('crm.app_name', config('app.name')) }}</h1>
                <div style="font-size: 11px; color: #475569;">
                    <div>{{ $companyData['address'] ?? 'Company Address' }}</div>
                    <div>{{ $companyData['phone'] ?? 'Phone N/A' }}</div>
                    <div>{{ $companyData['email'] ?? 'Email N/A' }}</div>
                    <div>{{ $companyData['vat_number'] ?? 'VAT N/A' }}</div>
                </div>
            </td>
            <td style="width: 260px; text-align: right; vertical-align: top;">
                <h2 style="margin: 0; font-size: 20px; letter-spacing: 1px;">QUOTE</h2>
                <div style="margin-top: 8px; font-size: 11px; color: #475569;">
                    <div><strong>Number:</strong> {{ $quote->number }}</div>
                    <div><strong>Date:</strong> {{ $quote->created_at?->toDateString() }}</div>
                    <div><strong>Valid Until:</strong> {{ $quote->valid_until?->toDateString() ?? '—' }}</div>
                </div>
            </td>
        </tr>
    </table>

    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
        <tr>
            <td style="width: 50%; vertical-align: top; border: 1px solid #e2e8f0; padding: 10px;">
                <div style="font-size: 11px; color: #475569; margin-bottom: 4px;">Bill To</div>
                <div style="font-weight: 600; color: #0f172a;">{{ $quote->account?->name ?? 'No account' }}</div>
                @if ($quote->contact)
                    <div>{{ trim(($quote->contact->first_name ?? '').' '.($quote->contact->last_name ?? '')) }}</div>
                    <div>{{ $quote->contact->email ?? '' }}</div>
                @endif
            </td>
            <td style="width: 50%; vertical-align: top; border: 1px solid #e2e8f0; padding: 10px;">
                <div style="font-size: 11px; color: #475569; margin-bottom: 4px;">Quote Info</div>
                <div><strong>Status:</strong> {{ $quote->status }}</div>
                <div><strong>Owner:</strong> {{ $quote->owner?->full_name ?? '—' }}</div>
                <div><strong>Currency:</strong> {{ $quote->currency }}</div>
            </td>
        </tr>
    </table>

    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
        <thead>
            <tr style="background: #f1f5f9; color: #334155;">
                <th style="border: 1px solid #e2e8f0; padding: 8px; text-align: left;">#</th>
                <th style="border: 1px solid #e2e8f0; padding: 8px; text-align: left;">Description</th>
                <th style="border: 1px solid #e2e8f0; padding: 8px; text-align: right;">Qty</th>
                <th style="border: 1px solid #e2e8f0; padding: 8px; text-align: right;">Unit Price</th>
                <th style="border: 1px solid #e2e8f0; padding: 8px; text-align: right;">Disc%</th>
                <th style="border: 1px solid #e2e8f0; padding: 8px; text-align: right;">Tax%</th>
                <th style="border: 1px solid #e2e8f0; padding: 8px; text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($quote->lineItems as $index => $lineItem)
                <tr>
                    <td style="border: 1px solid #e2e8f0; padding: 8px;">{{ $index + 1 }}</td>
                    <td style="border: 1px solid #e2e8f0; padding: 8px;">
                        <div style="font-weight: 600;">{{ $lineItem->name }}</div>
                        @if ($lineItem->description)
                            <div style="font-size: 10px; color: #64748b;">{{ $lineItem->description }}</div>
                        @endif
                    </td>
                    <td style="border: 1px solid #e2e8f0; padding: 8px; text-align: right;">{{ number_format((float) $lineItem->quantity, 2) }}</td>
                    <td style="border: 1px solid #e2e8f0; padding: 8px; text-align: right;">{{ number_format((float) $lineItem->unit_price, 2) }}</td>
                    <td style="border: 1px solid #e2e8f0; padding: 8px; text-align: right;">{{ number_format((float) $lineItem->discount_percent, 2) }}</td>
                    <td style="border: 1px solid #e2e8f0; padding: 8px; text-align: right;">{{ number_format((float) $lineItem->tax_rate, 2) }}</td>
                    <td style="border: 1px solid #e2e8f0; padding: 8px; text-align: right;">{{ number_format((float) $lineItem->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table style="width: 320px; margin-left: auto; border-collapse: collapse; margin-bottom: 20px;">
        <tr>
            <td style="padding: 6px; border: 1px solid #e2e8f0;">Subtotal</td>
            <td style="padding: 6px; border: 1px solid #e2e8f0; text-align: right;">{{ number_format((float) $quote->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td style="padding: 6px; border: 1px solid #e2e8f0;">Discount</td>
            <td style="padding: 6px; border: 1px solid #e2e8f0; text-align: right;">-{{ number_format((float) $quote->discount_amount, 2) }}</td>
        </tr>
        <tr>
            <td style="padding: 6px; border: 1px solid #e2e8f0;">Tax</td>
            <td style="padding: 6px; border: 1px solid #e2e8f0; text-align: right;">{{ number_format((float) $quote->tax_amount, 2) }}</td>
        </tr>
        <tr>
            <td style="padding: 8px; border: 1px solid #e2e8f0; font-weight: 700;">Grand Total</td>
            <td style="padding: 8px; border: 1px solid #e2e8f0; text-align: right; font-weight: 700;">{{ number_format((float) $quote->total, 2) }} {{ $quote->currency }}</td>
        </tr>
    </table>

    @if ($quote->notes)
        <div style="margin-top: 8px;">
            <h3 style="font-size: 12px; margin: 0 0 4px 0;">Notes / Terms</h3>
            <p style="margin: 0; color: #334155; white-space: pre-wrap;">{{ $quote->notes }}</p>
        </div>
    @endif

    <div style="margin-top: 30px; padding-top: 8px; border-top: 1px solid #e2e8f0; font-size: 10px; color: #64748b; text-align: center;">
        Thank you for your business.
        @if (! ($preview ?? false))
            <span style="float: right;">Page 1</span>
        @endif
    </div>
</div>
