@extends('layouts.app')
@section('title', 'Invoice List')
@section('content')

<div class="ktm-page-header">
    <div style="display:flex;align-items:center;gap:14px">
        <img src="{{ asset('images/R.png') }}" alt="KTM" style="height:36px;width:auto">
        <div>
            <h2 class="ktm-page-title" style="color:#064e3b">Invoice List</h2>
            <div class="ktm-page-sub">All vendor invoices submitted to KTMB — read only</div>
        </div>
    </div>
</div>

{{-- Summary cards --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px">
    <div class="ktm-card" style="padding:16px 20px;border-left:4px solid #1e3a8a">
        <div style="font-size:11px;color:var(--text-muted);font-weight:700;text-transform:uppercase;letter-spacing:.5px">Total Invoices</div>
        <div style="font-size:28px;font-weight:800;color:#1e3a8a;margin-top:4px">{{ $invoices->count() }}</div>
    </div>
    <div class="ktm-card" style="padding:16px 20px;border-left:4px solid #1d4ed8">
        <div style="font-size:11px;color:var(--text-muted);font-weight:700;text-transform:uppercase;letter-spacing:.5px">Submitted</div>
        <div style="font-size:28px;font-weight:800;color:#1d4ed8;margin-top:4px">{{ $invoices->where('InvoiceStatus','Submitted')->count() }}</div>
    </div>
    <div class="ktm-card" style="padding:16px 20px;border-left:4px solid #f59e0b">
        <div style="font-size:11px;color:var(--text-muted);font-weight:700;text-transform:uppercase;letter-spacing:.5px">Processing</div>
        <div style="font-size:28px;font-weight:800;color:#f59e0b;margin-top:4px">{{ $invoices->whereIn('InvoiceStatus',['Finance Review','Payment Processing'])->count() }}</div>
    </div>
    <div class="ktm-card" style="padding:16px 20px;border-left:4px solid #16a34a">
        <div style="font-size:11px;color:var(--text-muted);font-weight:700;text-transform:uppercase;letter-spacing:.5px">Paid</div>
        <div style="font-size:28px;font-weight:800;color:#16a34a;margin-top:4px">{{ $invoices->where('InvoiceStatus','Paid')->count() }}</div>
    </div>
</div>

{{-- Invoice table --}}
<div class="ktm-card">
    <div class="ktm-card-header" style="background:#064e3b;color:#fff;border-radius:13px 13px 0 0">
        <i class="fa fa-file-invoice me-2"></i>All Submitted Invoices
    </div>
    <div class="ktm-card-body" style="padding:0">
        @if($invoices->isEmpty())
        <div style="text-align:center;padding:40px;color:var(--text-muted)">
            <i class="fa fa-file-invoice" style="font-size:40px;margin-bottom:12px;opacity:.3"></i>
            <div>No invoices submitted yet.</div>
        </div>
        @else
        <table class="ktm-table">
            <thead>
                <tr>
                    <th>Invoice No</th>
                    <th>Vendor</th>
                    <th>DO Number</th>
                    <th>PO Number</th>
                    <th>Submitted Date</th>
                    <th style="text-align:right">Subtotal (RM)</th>
                    <th style="text-align:right">Tax (RM)</th>
                    <th style="text-align:right">Total (RM)</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoices as $invoice)
                <tr>
                    <td style="font-weight:700;color:#1e3a8a">{{ $invoice->InvoiceNumber }}</td>
                    <td>{{ $invoice->deliveryOrder->vendor->CompanyName ?? '—' }}</td>
                    <td>
                        <span style="background:#dbeafe;color:#1d4ed8;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700">
                            {{ $invoice->deliveryOrder->DONumber ?? '—' }}
                        </span>
                    </td>
                    <td style="color:var(--text-muted)">{{ $invoice->deliveryOrder->PONumber ?? '—' }}</td>
                    <td style="color:var(--text-muted);font-size:12px">
                        {{ $invoice->SubmittedDate ? \Carbon\Carbon::parse($invoice->SubmittedDate)->format('d M Y') : '—' }}
                    </td>
                    <td style="text-align:right">{{ number_format($invoice->Subtotal, 2) }}</td>
                    <td style="text-align:right;color:#1d4ed8">{{ number_format($invoice->Tax, 2) }}</td>
                    <td style="text-align:right;font-weight:700;color:#1e3a8a">{{ number_format($invoice->TotalAmount, 2) }}</td>
                    <td>
                        @if($invoice->InvoiceStatus === 'Paid')
                            <span class="badge-approved">Paid</span>
                        @elseif($invoice->InvoiceStatus === 'Submitted')
                            <span class="badge-submitted">Submitted</span>
                        @elseif($invoice->InvoiceStatus === 'Finance Review')
                            <span class="badge-review">Finance Review</span>
                        @elseif($invoice->InvoiceStatus === 'Payment Processing')
                            <span style="background:#fef3c7;color:#92400e;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700;display:inline-block">Processing</span>
                        @else
                            <span class="badge-review">{{ $invoice->InvoiceStatus }}</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>

{{-- Total claim summary --}}
@if($invoices->isNotEmpty())
<div class="ktm-card" style="margin-top:16px;padding:16px 20px">
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px">
        <div style="font-size:13px;color:var(--text-muted)">
            <i class="fa fa-info-circle me-1" style="color:#1e3a8a"></i>
            Total of {{ $invoices->count() }} invoice(s) from {{ $invoices->pluck('deliveryOrder.vendor.CompanyName')->unique()->count() }} vendor(s)
        </div>
        <div style="display:flex;gap:24px;font-size:13px">
            <div>Total Subtotal: <strong style="color:#1e3a8a">RM {{ number_format($invoices->sum('Subtotal'), 2) }}</strong></div>
            <div>Total Tax: <strong style="color:#1d4ed8">RM {{ number_format($invoices->sum('Tax'), 2) }}</strong></div>
            <div style="font-size:15px">Total Claim: <strong style="color:#1e3a8a;font-size:18px">RM {{ number_format($invoices->sum('TotalAmount'), 2) }}</strong></div>
        </div>
    </div>
</div>
@endif

@endsection
