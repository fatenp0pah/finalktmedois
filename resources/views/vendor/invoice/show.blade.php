@extends('layouts.app')
@section('title', 'Invoice ' . $invoice->InvoiceNumber)
@section('content')

{{-- Action bar (hidden on print) --}}
<div class="ktm-page-header no-print">
    <div style="display:flex;align-items:center;gap:14px">
        <img src="{{ asset('images/R.png') }}" alt="KTM" style="height:36px;width:auto">
        <div>
            <h2 class="ktm-page-title" style="color:#1e3a8a">Invoice Detail</h2>
            <div class="ktm-page-sub">{{ $invoice->InvoiceNumber }}</div>
        </div>
    </div>
    <div style="display:flex;gap:10px">
        <a href="{{ route('vendor.invoice.index') }}"
           style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;
                  background:transparent;color:#1e3a8a;border:1.5px solid #1e3a8a;
                  border-radius:9px;font-size:13px;font-weight:700;text-decoration:none">
            <i class="fa fa-arrow-left me-1"></i> Back
        </a>
        <button onclick="window.print()"
           style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;
                  background:linear-gradient(135deg,#1e3a8a,#1e40af);color:#fff;
                  border:none;border-radius:9px;font-size:13px;font-weight:700;cursor:pointer">
            <i class="fa fa-print"></i> Print Invoice
        </button>
    </div>
</div>

{{-- ── INVOICE DOCUMENT ── --}}
<div class="invoice-doc" id="invoice-print">

    {{-- Header --}}
    <div style="display:flex;justify-content:space-between;align-items:flex-start;
                margin-bottom:24px">

        {{-- Left: KTM Logo + Title --}}
        <div style="display:flex;flex-direction:column;align-items:flex-start;gap:10px">
            <img src="{{ asset('images/R.png') }}" alt="KTM Logo"
                 style="height:60px;width:auto">
            <div style="font-size:32px;font-weight:900;color:#1e3a8a;letter-spacing:1px">
                INVOICE
            </div>
        </div>

        {{-- Right: KTMB Details --}}
        <div style="text-align:right;font-size:12px;color:#374151;line-height:1.8">
            <div style="font-size:14px;font-weight:700;color:#1e3a8a;margin-bottom:4px">
                Keretapi Tanah Melayu Berhad
            </div>
            <div>KTMB Headquarters</div>
            <div>Jalan Sultan Hishamuddin</div>
            <div>50621 Kuala Lumpur</div>
            <div>Company Registration No: 199101015631</div>
            <div>SST No : W10-1808-31002103</div>
            <div>Supplier TIN : C4893200000</div>
            <div>Tel : 03-2263 1111</div>
            <div>Web : <span style="color:#1d4ed8">www.ktmb.com.my</span></div>
        </div>
    </div>

    <div class="inv-divider"></div>

    {{-- Bill-to / Invoice Info --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:16px">

        {{-- Bill-to --}}
        <div>
            <div style="font-size:11px;font-weight:700;color:#6b7280;
                        text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">
                Bill-to
            </div>
            <div style="font-size:13px;font-weight:700;color:#111827;margin-bottom:4px">
                {{ $invoice->deliveryOrder->vendor->CompanyName ?? '—' }}
            </div>
            <div style="font-size:12px;color:#374151;line-height:1.7">
                {{ $invoice->deliveryOrder->ShippingAddress ?? '—' }}<br>
                Buyer TIN : {{ $invoice->deliveryOrder->vendor->RefNumber ?? '—' }}
            </div>
        </div>

        {{-- Invoice Info --}}
        <div style="text-align:right">
            <table style="width:100%;font-size:12px;border-collapse:collapse;margin-left:auto">
                <tr>
                    <td style="color:#6b7280;padding:3px 8px 3px 0;text-align:right">Invoice No</td>
                    <td style="font-weight:600;color:#111827;padding:3px 0;text-align:right">
                        {{ $invoice->InvoiceNumber }}
                    </td>
                </tr>
                <tr>
                    <td style="color:#6b7280;padding:3px 8px 3px 0;text-align:right">Invoice Date</td>
                    <td style="font-weight:600;color:#111827;padding:3px 0;text-align:right">
                        {{ $invoice->SubmittedDate
                            ? \Carbon\Carbon::parse($invoice->SubmittedDate)->timezone('Asia/Kuala_Lumpur')->format('d M Y')
                            : '—' }}
                    </td>
                </tr>
                <tr>
                    <td style="color:#6b7280;padding:3px 8px 3px 0;text-align:right">DO Reference</td>
                    <td style="font-weight:600;color:#111827;padding:3px 0;text-align:right">
                        {{ $invoice->deliveryOrder->DONumber ?? '—' }}
                    </td>
                </tr>
                <tr>
                    <td style="color:#6b7280;padding:3px 8px 3px 0;text-align:right">PO Number</td>
                    <td style="font-weight:600;color:#111827;padding:3px 0;text-align:right">
                        {{ $invoice->deliveryOrder->PONumber ?? '—' }}
                    </td>
                </tr>
                <tr>
                    <td style="color:#6b7280;padding:3px 8px 3px 0;text-align:right">Status</td>
                    <td style="padding:3px 0;text-align:right">
                        @if($invoice->InvoiceStatus === 'Paid')
                            <span style="background:#dcfce7;color:#16a34a;padding:2px 10px;border-radius:20px;font-size:11px;font-weight:700">Paid</span>
                        @elseif($invoice->InvoiceStatus === 'Submitted')
                            <span style="background:#dbeafe;color:#1d4ed8;padding:2px 10px;border-radius:20px;font-size:11px;font-weight:700">Submitted</span>
                        @else
                            <span style="background:#fef3c7;color:#92400e;padding:2px 10px;border-radius:20px;font-size:11px;font-weight:700">{{ $invoice->InvoiceStatus }}</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>

    </div>

    {{-- Customer / Summary row --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:0">
        <div style="font-size:12px;color:#374151;line-height:1.8">
            <span style="color:#6b7280">Vendor No</span>
            <span style="font-weight:600;margin-left:12px">
                {{ $invoice->deliveryOrder->vendor->VendorNumber ?? '—' }}
            </span><br>
            <span style="color:#6b7280">Project Ref</span>
            <span style="font-weight:600;margin-left:12px">
                {{ $invoice->deliveryOrder->ProjectReference ?? '—' }}
            </span>
        </div>

        {{-- Right: Line Total / Tax / Subtotals --}}
        <div>
            <table style="width:100%;font-size:12px;border-collapse:collapse">
                <tr>
                    <td style="color:#6b7280;padding:3px 0;text-align:right;padding-right:20px">Line Total</td>
                    <td style="font-weight:600;color:#111827;padding:3px 0;text-align:right">
                        {{ number_format($invoice->Subtotal, 2) }}
                    </td>
                </tr>
                <tr>
                    <td style="color:#6b7280;padding:3px 0;text-align:right;padding-right:20px">Service Tax (6%)</td>
                    <td style="font-weight:600;color:#111827;padding:3px 0;text-align:right">
                        {{ number_format($invoice->Tax, 2) }}
                    </td>
                </tr>
                @if($invoice->Discount > 0)
                <tr>
                    <td style="color:#6b7280;padding:3px 0;text-align:right;padding-right:20px">Discount</td>
                    <td style="font-weight:600;color:#16a34a;padding:3px 0;text-align:right">
                        -{{ number_format($invoice->Discount, 2) }}
                    </td>
                </tr>
                @endif
                @if($invoice->Penalty > 0)
                <tr>
                    <td style="color:#6b7280;padding:3px 0;text-align:right;padding-right:20px">Penalty</td>
                    <td style="font-weight:600;color:#dc2626;padding:3px 0;text-align:right">
                        -{{ number_format($invoice->Penalty, 2) }}
                    </td>
                </tr>
                @endif
            </table>
        </div>
    </div>

    <div class="inv-divider"></div>

    {{-- Total summary box --}}
    <div style="display:flex;justify-content:flex-end;margin-bottom:16px">
        <table style="font-size:12px;border-collapse:collapse;min-width:260px">
            <tr>
                <td style="color:#6b7280;padding:4px 20px 4px 0;text-align:right">Total</td>
                <td style="color:#111827;padding:4px 0;text-align:right;font-weight:600">
                    {{ number_format($invoice->TotalAmount, 2) }}
                </td>
            </tr>
            <tr>
                <td style="color:#6b7280;padding:4px 20px 4px 0;text-align:right">Payments</td>
                <td style="color:#111827;padding:4px 0;text-align:right">
                    {{ $invoice->InvoiceStatus === 'Paid' ? '-' . number_format($invoice->TotalAmount, 2) : '0.00' }}
                </td>
            </tr>
            <tr>
                <td style="color:#6b7280;padding:4px 20px 4px 0;text-align:right">Credits</td>
                <td style="color:#111827;padding:4px 0;text-align:right">0.00</td>
            </tr>
            <tr>
                <td style="color:#6b7280;padding:4px 20px 4px 0;text-align:right">Financial Charges</td>
                <td style="color:#111827;padding:4px 0;text-align:right">0.00</td>
            </tr>
        </table>
    </div>

    {{-- Payment Terms / Balance Due bar --}}
    <div style="display:flex;align-items:center;justify-content:space-between;
                background:#f1f5f9;border:1px solid #e2e8f0;border-radius:8px;
                padding:12px 16px;margin-bottom:20px">
        <div style="font-size:12px;color:#374151">
            <strong>Payment Terms</strong> &nbsp;30 DAYS &nbsp;&nbsp;
            <strong>Due Date</strong> &nbsp;
            {{ $invoice->SubmittedDate
                ? \Carbon\Carbon::parse($invoice->SubmittedDate)->addDays(30)->timezone('Asia/Kuala_Lumpur')->format('d M Y')
                : '—' }}
        </div>
        <div style="font-size:16px;font-weight:800;color:#1e3a8a">
            Balance Due &nbsp;
            <span style="color:{{ $invoice->InvoiceStatus === 'Paid' ? '#16a34a' : '#dc2626' }}">
                RM {{ $invoice->InvoiceStatus === 'Paid' ? '0.00' : number_format($invoice->TotalAmount, 2) }}
            </span>
        </div>
    </div>

    {{-- Line Items Table --}}
    <table style="width:100%;border-collapse:collapse;font-size:12px;margin-bottom:20px">
        <thead>
            <tr style="background:#1e3a8a;color:#fff">
                <th style="padding:10px 12px;text-align:left;width:5%">No.</th>
                <th style="padding:10px 12px;text-align:left;width:10%">DO No.</th>
                <th style="padding:10px 12px;text-align:left">Description</th>
                <th style="padding:10px 12px;text-align:right;width:10%">Quantity</th>
                <th style="padding:10px 12px;text-align:right;width:12%">Unit Price (RM)</th>
                <th style="padding:10px 12px;text-align:right;width:12%">Amount (RM)</th>
            </tr>
        </thead>
        <tbody>
            <tr style="border-bottom:1px solid #e2e8f0">
                <td style="padding:12px;color:#374151">1</td>
                <td style="padding:12px;color:#374151">
                    {{ $invoice->deliveryOrder->DONumber ?? '—' }}
                </td>
                <td style="padding:12px;color:#374151">
                    {{ $invoice->InvoiceDescription ?: ($invoice->deliveryOrder->ItemDescription ?? 'Delivery Order Claim') }}
                    @if($invoice->deliveryOrder->DeliveryDate)
                    <div style="font-size:11px;color:#6b7280;margin-top:2px">
                        Delivery Date: {{ \Carbon\Carbon::parse($invoice->deliveryOrder->DeliveryDate)->format('d M Y') }}
                    </div>
                    @endif
                </td>
                <td style="padding:12px;text-align:right;color:#374151">
                    {{ $invoice->deliveryOrder->Quantity ?? 1 }}
                </td>
                <td style="padding:12px;text-align:right;color:#374151">
                    {{ number_format($invoice->Subtotal / max(1, $invoice->deliveryOrder->Quantity ?? 1), 2) }}
                </td>
                <td style="padding:12px;text-align:right;font-weight:600;color:#111827">
                    {{ number_format($invoice->Subtotal, 2) }}
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr style="background:#f8fafc">
                <td colspan="5" style="padding:10px 12px;text-align:right;
                            font-weight:600;color:#374151">Line Total</td>
                <td style="padding:10px 12px;text-align:right;
                           font-weight:700;color:#1e3a8a">
                    {{ number_format($invoice->Subtotal, 2) }}
                </td>
            </tr>
        </tfoot>
    </table>

    <div class="inv-divider"></div>

    {{-- Footer --}}
    <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-top:16px">
        {{-- Payment info --}}
        <div style="font-size:12px;color:#374151;line-height:1.8">
            <div style="font-weight:700;margin-bottom:4px">Please make payment to :</div>
            <div>Account Name : KERETAPI TANAH MELAYU BERHAD</div>
            <div>Acc. No &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: 514011336586</div>
            <div>Bank Name &nbsp;: Malayan Banking Bhd</div>
            <div style="margin-top:12px;font-weight:700">Salesperson</div>
        </div>

        {{-- QR / Stamp area --}}
        <div style="text-align:center">
            <div style="width:80px;height:80px;border:2px dashed #cbd5e1;
                        border-radius:8px;display:flex;align-items:center;
                        justify-content:center;color:#9ca3af;font-size:10px;
                        margin-bottom:8px">
                QR Code
            </div>
            <div style="font-size:10px;color:#9ca3af">Company Stamp</div>
        </div>
    </div>

    {{-- Page footer --}}
    <div style="margin-top:30px;padding-top:12px;border-top:1px solid #e2e8f0;
                text-align:center;font-size:10px;color:#9ca3af">
        Page 1 of 1 &nbsp;&nbsp;|&nbsp;&nbsp;
        THIS IS A COMPUTER GENERATED INVOICE. NO SIGNATURE REQUIRED &nbsp;&nbsp;|&nbsp;&nbsp;
        KTMeDOIS — {{ now()->format('Y') }}
    </div>

</div>

@push('styles')
<style>
    .invoice-doc {
        background: #fff;
        max-width: 900px;
        margin: 0 auto;
        padding: 40px;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        font-family: 'Segoe UI', Arial, sans-serif;
    }
    .inv-divider {
        border: none;
        border-top: 1px solid #e2e8f0;
        margin: 16px 0;
    }
    @media print {
        .no-print { display: none !important; }
        .ktm-sidebar, .ktm-topbar, .ktm-main > *:not(#invoice-print) { display: none !important; }
        .ktm-main { margin-left: 0 !important; }
        .ktm-content { padding: 0 !important; }
        .invoice-doc {
            border: none;
            box-shadow: none;
            padding: 20px;
            max-width: 100%;
        }
        body { background: white !important; }
    }
</style>
@endpush

@endsection
