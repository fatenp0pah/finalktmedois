@extends('layouts.app')
@section('title', 'My Invoices')
@section('content')

{{-- Page header --}}
<div class="ktm-page-header">
    <div style="display:flex;align-items:center;gap:14px">
        <img src="{{ asset('images/R.png') }}" alt="KTM" style="height:36px;width:auto">
        <div>
            <h2 class="ktm-page-title" style="color:#1e3a8a">My Invoices</h2>
            <div class="ktm-page-sub">Invoice Submission Module [KTMeDOIS-2026-V1-R300]</div>
        </div>
    </div>
    @if(Auth::user()->vendor->isActive())
    <a href="{{ route('vendor.invoice.create') }}"
       style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;
              background:linear-gradient(135deg,#1e3a8a,#1e40af);color:#fff;
              border-radius:9px;font-size:13px;font-weight:700;text-decoration:none">
        <i class="fa fa-plus"></i> Submit New Invoice
    </a>
    @endif
</div>

{{-- Inactive warning --}}
@if(!Auth::user()->vendor->isActive())
<div class="ktm-alert ktm-alert-danger mb-4">
    <i class="fa fa-exclamation-triangle mt-1" style="flex-shrink:0"></i>
    <span>Your vendor account is <strong>{{ Auth::user()->vendor->VendorStatus }}</strong>.
    Invoice submission is disabled. Contact KTMB Procurement to reactivate.</span>
</div>
@endif

{{-- Use Livewire component to list invoices --}}
@livewire('vendor.all-invoice')

@endsection
