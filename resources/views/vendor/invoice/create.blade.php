@extends('layouts.app')
@section('title', 'Submit Invoice')
@section('content')

{{-- Page header --}}
<div class="ktm-page-header">
    <div style="display:flex;align-items:center;gap:14px">
        <img src="{{ asset('images/R.png') }}" alt="KTM" style="height:36px;width:auto">
        <div>
            <h2 class="ktm-page-title" style="color:#1e3a8a">Submit Invoice</h2>
            <div class="ktm-page-sub">Invoice Submission Module [KTMeDOIS-2026-V1-R300]</div>
        </div>
    </div>
    <a href="{{ route('vendor.invoice.index') }}"
       style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;
              background:transparent;color:#1e3a8a;border:1.5px solid #1e3a8a;
              border-radius:9px;font-size:13px;font-weight:700;text-decoration:none">
        <i class="fa fa-arrow-left me-1"></i> My Invoices
    </a>
</div>

@if(!Auth::user()->vendor->isActive())
{{-- Block inactive vendors from seeing the form --}}
<div class="ktm-alert ktm-alert-danger mb-4">
    <i class="fa fa-lock mt-1" style="flex-shrink:0"></i>
    <span>Your vendor account is <strong>{{ Auth::user()->vendor->VendorStatus }}</strong>.
    Invoice submission is not permitted. Contact KTMB Procurement to reactivate your account.</span>
</div>
@else

<div class="row g-4">

    {{-- ── Left: Livewire Form ── --}}
    <div class="col-lg-8">
        <div class="ktm-card">
            <div class="ktm-card-header"
                 style="background:#1e3a8a;color:#fff;border-radius:13px 13px 0 0">
                <i class="fa fa-file-invoice me-2"></i>Invoice Details
            </div>
            <div class="ktm-card-body">
                @livewire('vendor.submit')
            </div>
        </div>
    </div>

    {{-- ── Right: Calculation Info ── --}}
    <div class="col-lg-4">
        <div class="ktm-card h-100">
            <div class="ktm-card-header"
                 style="background:#1e3a8a;color:#fff;border-radius:13px 13px 0 0">
                <i class="fa fa-calculator me-2"></i>Calculation Guide
            </div>
            <div class="ktm-card-body">
                <div style="font-size:12px;color:var(--text-muted);margin-bottom:16px">
                    All amounts are auto-calculated based on your entered subtotal.
                </div>

                <div style="display:flex;flex-direction:column;gap:12px">
                    <div style="background:#eff6ff;border-radius:10px;padding:12px 14px">
                        <div style="font-size:12px;font-weight:700;color:#1e3a8a;margin-bottom:4px">
                            <i class="fa fa-percent me-1"></i>Tax (6%)
                        </div>
                        <div style="font-size:11px;color:#1e40af">
                            Automatically calculated as 6% of the subtotal amount.
                        </div>
                    </div>

                    <div style="background:#fef3c7;border-radius:10px;padding:12px 14px">
                        <div style="font-size:12px;font-weight:700;color:#92400e;margin-bottom:4px">
                            <i class="fa fa-tag me-1"></i>Discount
                        </div>
                        <div style="font-size:11px;color:#92400e">
                            Enter any credit note or negotiated discount amount manually.
                        </div>
                    </div>

                    <div style="background:#fee2e2;border-radius:10px;padding:12px 14px">
                        <div style="font-size:12px;font-weight:700;color:#991b1b;margin-bottom:4px">
                            <i class="fa fa-exclamation-triangle me-1"></i>Penalty (1%)
                        </div>
                        <div style="font-size:11px;color:#991b1b">
                            Tick "Late Delivery" if delivery was delayed. 1% of subtotal will be deducted.
                        </div>
                    </div>

                    <div style="background:#1e3a8a;border-radius:10px;padding:14px">
                        <div style="font-size:12px;font-weight:700;color:#fbbf24;margin-bottom:6px">
                            <i class="fa fa-calculator me-1"></i>Total Formula
                        </div>
                        <div style="font-size:11px;color:rgba(255,255,255,0.8);line-height:1.8">
                            Subtotal<br>
                            + Tax (6%)<br>
                            − Discount<br>
                            − Penalty (1% if late)<br>
                            <div style="border-top:1px solid rgba(255,255,255,0.2);margin-top:6px;padding-top:6px;color:#fbbf24;font-weight:700">
                                = Total Claim Amount
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ktm-alert ktm-alert-info mt-3 mb-0" style="font-size:11px">
                    <i class="fa fa-info-circle mt-1" style="flex-shrink:0;color:#1e3a8a"></i>
                    <span>Only DOs with <strong>Approved</strong> status can be invoiced.
                    Each approved DO can only have one invoice.</span>
                </div>
            </div>
        </div>
    </div>

</div>

@endif

@endsection
