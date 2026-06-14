{{-- detail.blade.php --}}
@extends('layouts.app')
@section('title', 'DO Detail — ' . $selectedDO->DONumber)
@section('content')
@php $s = $selectedDO->DOStatus; @endphp
<div class="ktm-page-header">
    <div>
        <h2 class="ktm-page-title"><i class="fa fa-file-alt me-2" style="color:var(--primary)"></i>{{ $selectedDO->DONumber }}</h2>
        <div class="ktm-page-sub">Delivery Order Detail View</div>
    </div>
    <div style="display:flex;align-items:center;gap:12px">
        @if($s === 'Approved') <span class="badge-approved">Approved</span>
        @elseif($s === 'Rejected') <span class="badge-rejected">Rejected</span>
        @elseif($s === 'Submitted') <span class="badge-submitted">Submitted</span>
        @elseif($s === 'Under Review') <span class="badge-review">Under Review</span>
        @else <span class="badge-inactive">Draft</span>
        @endif
        <button onclick="window.print()" class="btn-ktm-outline"><i class="fa fa-print me-1"></i>Print</button>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="ktm-card mb-4">
            <div class="ktm-card-header"><i class="fa fa-info-circle me-2"></i>DO Information</div>
            <div class="ktm-card-body">
                <div class="row g-3">
                    @foreach([['DO Number','DONumber'],['PO Number','PONumber'],['Project Reference','ProjectReference'],['Customer','Customer'],['Submitted Date','SubmittedDate'],['Delivery Date','DeliveryDate']] as [$label,$field])
                    <div class="col-md-6">
                        <label class="ktm-label">{{ $label }}</label>
                        <div class="profile-field-val">{{ $selectedDO->$field ?? '—' }}</div>
                    </div>
                    @endforeach
                    <div class="col-12">
                        <label class="ktm-label">Item Description</label>
                        <div class="profile-field-val">{{ $selectedDO->ItemDescription ?? '—' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="ktm-label">Item No</label>
                        <div class="profile-field-val">{{ $selectedDO->ItemNo ?? '—' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="ktm-label">Quantity</label>
                        <div class="profile-field-val">{{ $selectedDO->Quantity ?? '—' }}</div>
                    </div>
                    <div class="col-12">
                        <label class="ktm-label">Shipping Address</label>
                        <div class="profile-field-val">{{ $selectedDO->ShippingAddress ?? '—' }}</div>
                    </div>
                    <div class="col-12">
                        <label class="ktm-label">Invoice Address</label>
                        <div class="profile-field-val">{{ $selectedDO->InvoiceAddress ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="ktm-card mb-4">
            <div class="ktm-card-header"><i class="fa fa-upload me-2"></i>Uploaded Documents</div>
            <div class="ktm-card-body row g-3">
                <div class="col-md-6">
                    <label class="ktm-label">DO Document</label>
                    @if($selectedDO->DOFileLink)
                        <a href="{{ asset('storage/' . $selectedDO->DOFileLink) }}" target="_blank" class="btn-ktm d-block text-center">
                            <i class="fa fa-file-pdf me-1"></i>View Document
                        </a>
                    @else
                        <div class="profile-field-val" style="color:var(--text-muted)">No document uploaded</div>
                    @endif
                </div>
                <div class="col-md-6">
                    <label class="ktm-label">Proof of Delivery</label>
                    @if($selectedDO->ProofFileLink)
                        <a href="{{ asset('storage/' . $selectedDO->ProofFileLink) }}" target="_blank" class="btn-ktm d-block text-center">
                            <i class="fa fa-image me-1"></i>View Proof
                        </a>
                    @else
                        <div class="profile-field-val" style="color:var(--text-muted)">No proof uploaded</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="ktm-card mb-4">
            <div class="ktm-card-header"><i class="fa fa-building me-2"></i>Vendor Info</div>
            <div class="ktm-card-body">
                @if($selectedDO->vendor)
                <div style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:10px">{{ $selectedDO->vendor->CompanyName }}</div>
                <div style="font-size:12px;color:var(--text-muted)">{{ $selectedDO->vendor->VendorNumber }}</div>
                <div style="font-size:12px;color:var(--text-muted)">{{ $selectedDO->vendor->VendorEmail }}</div>
                @endif
            </div>
        </div>

        <div class="ktm-card">
            <div class="ktm-card-header"><i class="fa fa-comment me-2"></i>Officer Remark</div>
            <div class="ktm-card-body">
                <div style="background:var(--body-bg);border-left:4px solid var(--primary);padding:14px;border-radius:8px;font-size:13px;color:var(--text)">
                    {{ $selectedDO->Remark ?? 'No remark yet.' }}
                </div>
                @if($selectedDO->invoice)
                <div style="margin-top:16px">
                    <label class="ktm-label">Linked Invoice</label>
                    <div class="profile-field-val">{{ $selectedDO->invoice->InvoiceNumber }} — {{ $selectedDO->invoice->InvoiceStatus }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div style="display:flex;gap:12px;justify-content:flex-end;margin-top:8px">
    @if(Auth::user()->isVendor())
    <a href="{{ route('vendor.do.dashboard') }}" class="btn-ktm-outline"><i class="fa fa-arrow-left me-1"></i>Back</a>
    <a href="{{ route('vendor.do.status') }}" class="btn-ktm"><i class="fa fa-tasks me-1"></i>Status Tracking</a>
    @else
    <a href="{{ route('officer.dashboard') }}" class="btn-ktm-outline"><i class="fa fa-arrow-left me-1"></i>Back</a>
    <a href="{{ route('officer.do.review') }}" class="btn-ktm"><i class="fa fa-clipboard-check me-1"></i>Review Page</a>
    @endif
</div>

@push('styles')
<style>
.profile-field-val { display:flex;align-items:center;gap:8px;font-size:14px;font-weight:600;color:var(--text);background:var(--body-bg);border:1px solid var(--card-border);border-radius:9px;padding:10px 14px; }
@media print { .ktm-page-header .btn-ktm-outline, button { display:none!important; } }
</style>
@endpush
@endsection
