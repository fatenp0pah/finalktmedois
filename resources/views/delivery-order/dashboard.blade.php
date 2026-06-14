@extends('layouts.app')
@section('title', Auth::user()->isOfficer() ? 'Officer Dashboard' : 'DO Dashboard')
@section('content')

@php $isOfficer = Auth::user()->isOfficer(); @endphp

{{-- Page header --}}
<div class="ktm-page-header">
    <div style="display:flex;align-items:center;gap:14px">
        <img src="{{ asset('images/R.png') }}" alt="KTM" style="height:36px;width:auto">
        <div>
            @if($isOfficer)
            <h2 class="ktm-page-title" style="color:#065f46">Officer Dashboard</h2>
            <div class="ktm-page-sub">Internal Review & Approval — All Vendor Delivery Orders</div>
            @else
            <h2 class="ktm-page-title" style="color:#1e3a8a">My Delivery Orders</h2>
            <div class="ktm-page-sub">Module 2 — Delivery Order Submission [KTMeDOIS-2026-V1-R200]</div>
            @endif
        </div>
    </div>
    <div style="display:flex;gap:10px">
        @if(!$isOfficer && Auth::user()->vendor->isActive())
        <a href="{{ route('vendor.do.create') }}"
           style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;
                  background:linear-gradient(135deg,#1e3a8a,#1e40af);color:#fff;
                  border-radius:9px;font-size:13px;font-weight:700;text-decoration:none">
            <i class="fa fa-plus"></i> Create New DO
        </a>
        @endif
        @if($isOfficer)
        <a href="{{ route('officer.do.review') }}"
           style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;
                  background:linear-gradient(135deg,#064e3b,#065f46);color:#fff;
                  border-radius:9px;font-size:13px;font-weight:700;text-decoration:none">
            <i class="fa fa-clipboard-check"></i> Review DOs
        </a>
        @endif
        <a href="{{ $isOfficer ? route('officer.do.report') : route('vendor.do.report') }}"
           style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;
                  background:transparent;color:{{ $isOfficer ? '#065f46' : '#1e3a8a' }};
                  border:1.5px solid {{ $isOfficer ? '#065f46' : '#1e3a8a' }};
                  border-radius:9px;font-size:13px;font-weight:700;text-decoration:none">
            <i class="fa fa-file-alt"></i> Report
        </a>
    </div>
</div>

@if(session('success'))
<div class="ktm-alert ktm-alert-success mb-4">
    <i class="fa fa-check-circle mt-1" style="flex-shrink:0"></i>
    <span>{{ session('success') }}</span>
</div>
@endif
@if(session('error'))
<div class="ktm-alert ktm-alert-danger mb-4">
    <i class="fa fa-exclamation-circle mt-1" style="flex-shrink:0"></i>
    <span>{{ session('error') }}</span>
</div>
@endif

{{-- ── Summary Cards ── --}}
{{-- Officer: no Draft card (drafts are vendor-side only, not yet submitted) --}}
{{-- Vendor: shows all stages including Draft --}}
@php $accent = $isOfficer ? '#065f46' : '#1e3a8a'; @endphp

<div class="row g-3 mb-4">
    @if($isOfficer)
        @foreach([
            ['Total DOs',    $summary['total'],       'fa-list',         '#dbeafe', '#1d4ed8'],
            ['Submitted',    $summary['submitted'],    'fa-paper-plane',  '#dbeafe', '#1d4ed8'],
            ['Under Review', $summary['underReview'],  'fa-search',       '#fef3c7', '#92400e'],
            ['Approved',     $summary['approved'],     'fa-check-circle', '#dcfce7', '#16a34a'],
            ['Rejected',     $summary['rejected'],     'fa-times-circle', '#fee2e2', '#dc2626'],
        ] as [$label, $val, $icon, $bg, $ic])
        <div class="col-6 col-md-4 col-lg">
            <div style="background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:18px;
                        box-shadow:0 1px 4px rgba(0,0,0,0.05);text-align:center">
                <div style="width:42px;height:42px;border-radius:10px;background:{{ $bg }};
                            display:flex;align-items:center;justify-content:center;margin:0 auto 10px">
                    <i class="fa {{ $icon }}" style="color:{{ $ic }};font-size:18px"></i>
                </div>
                <div style="font-size:26px;font-weight:800;color:#065f46">{{ $val }}</div>
                <div style="font-size:11px;color:#6b7280;margin-top:3px">{{ $label }}</div>
            </div>
        </div>
        @endforeach
    @else
        @foreach([
            ['Total DOs',    $summary['total'],       'fa-list',         '#dbeafe', '#1d4ed8'],
            ['Draft',        $summary['draft'],        'fa-pencil-alt',   '#e5e7eb', '#374151'],
            ['Submitted',    $summary['submitted'],    'fa-paper-plane',  '#dbeafe', '#1d4ed8'],
            ['Under Review', $summary['underReview'],  'fa-search',       '#fef3c7', '#92400e'],
            ['Approved',     $summary['approved'],     'fa-check-circle', '#dcfce7', '#16a34a'],
            ['Rejected',     $summary['rejected'],     'fa-times-circle', '#fee2e2', '#dc2626'],
        ] as [$label, $val, $icon, $bg, $ic])
        <div class="col-6 col-md-4 col-lg-2">
            <div style="background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:18px;
                        box-shadow:0 1px 4px rgba(0,0,0,0.05);text-align:center">
                <div style="width:42px;height:42px;border-radius:10px;background:{{ $bg }};
                            display:flex;align-items:center;justify-content:center;margin:0 auto 10px">
                    <i class="fa {{ $icon }}" style="color:{{ $ic }};font-size:18px"></i>
                </div>
                <div style="font-size:26px;font-weight:800;color:#1e3a8a">{{ $val }}</div>
                <div style="font-size:11px;color:#6b7280;margin-top:3px">{{ $label }}</div>
            </div>
        </div>
        @endforeach
    @endif
</div>

{{-- ── Officer: Pending Review Alert ── --}}
@if($isOfficer && $summary['submitted'] > 0)
<div style="background:linear-gradient(135deg,#064e3b,#065f46);border-radius:14px;
            padding:18px 24px;margin-bottom:20px;display:flex;align-items:center;
            justify-content:space-between;gap:16px;flex-wrap:wrap">
    <div style="display:flex;align-items:center;gap:14px">
        <div style="width:48px;height:48px;border-radius:12px;background:rgba(255,255,255,0.15);
                    display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="fa fa-bell" style="color:#34d399;font-size:22px"></i>
        </div>
        <div>
            <div style="color:#fff;font-size:15px;font-weight:700">
                {{ $summary['submitted'] }} Delivery Order(s) Awaiting Review
            </div>
            <div style="color:rgba(255,255,255,0.7);font-size:12px;margin-top:2px">
                Vendors are waiting for your approval. Review now to keep the process moving.
            </div>
        </div>
    </div>
    <a href="{{ route('officer.do.review') }}"
       style="display:inline-flex;align-items:center;gap:6px;padding:9px 20px;
              background:#34d399;color:#022c22;border-radius:9px;font-size:13px;
              font-weight:700;text-decoration:none;flex-shrink:0">
        <i class="fa fa-clipboard-check"></i> Review Now
    </a>
</div>
@endif

{{-- ── Vendor: Draft reminder if any drafts exist ── --}}
@if(!$isOfficer && $summary['draft'] > 0)
<div style="background:#fffbeb;border:1px solid #fde68a;border-radius:14px;
            padding:14px 20px;margin-bottom:20px;display:flex;align-items:center;
            justify-content:space-between;gap:16px;flex-wrap:wrap">
    <div style="display:flex;align-items:center;gap:12px">
        <i class="fa fa-pencil-alt" style="color:#d97706;font-size:20px"></i>
        <div>
            <div style="font-size:13px;font-weight:700;color:#92400e">
                You have {{ $summary['draft'] }} unsent Draft DO(s)
            </div>
            <div style="font-size:12px;color:#b45309;margin-top:2px">
                Draft DOs are not visible to officers. Submit them when ready.
            </div>
        </div>
    </div>
    <a href="{{ route('vendor.do.create') }}"
       style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;
              background:#d97706;color:#fff;border-radius:9px;font-size:12px;
              font-weight:700;text-decoration:none;flex-shrink:0">
        <i class="fa fa-plus"></i> Create New DO
    </a>
</div>
@endif

{{-- ── DO Table ── --}}
<div style="background:#fff;border:1px solid #e5e7eb;border-radius:14px;
            box-shadow:0 1px 4px rgba(0,0,0,0.05)">
    <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;display:flex;
                align-items:center;justify-content:space-between;
                background:{{ $isOfficer ? '#064e3b' : '#1e3a8a' }};
                border-radius:13px 13px 0 0">
        <div style="display:flex;align-items:center;gap:10px">
            <img src="{{ asset('images/R.png') }}" alt="KTM"
                 style="height:22px;filter:brightness(0) invert(1)">
            <span style="font-size:14px;font-weight:700;color:#fff">
                {{ $isOfficer ? 'All Delivery Orders (Excluding Drafts)' : 'My Delivery Orders' }}
            </span>
        </div>
        <span style="font-size:11px;color:rgba(255,255,255,0.6)">
            {{ $deliveryOrders->count() }} record(s)
        </span>
    </div>

    <div style="overflow-x:auto">
        <table style="width:100%;border-collapse:collapse;font-size:13px">
            <thead>
                <tr style="background:#f8fafc">
                    <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;
                               color:#6b7280;text-transform:uppercase;letter-spacing:.5px;
                               border-bottom:1px solid #e5e7eb">DO Number</th>
                    @if($isOfficer)
                    <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;
                               color:#6b7280;text-transform:uppercase;letter-spacing:.5px;
                               border-bottom:1px solid #e5e7eb">Vendor</th>
                    @endif
                    <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;
                               color:#6b7280;text-transform:uppercase;letter-spacing:.5px;
                               border-bottom:1px solid #e5e7eb">PO Number</th>
                    <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;
                               color:#6b7280;text-transform:uppercase;letter-spacing:.5px;
                               border-bottom:1px solid #e5e7eb">Project Ref</th>
                    <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;
                               color:#6b7280;text-transform:uppercase;letter-spacing:.5px;
                               border-bottom:1px solid #e5e7eb">Submitted</th>
                    <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;
                               color:#6b7280;text-transform:uppercase;letter-spacing:.5px;
                               border-bottom:1px solid #e5e7eb">Status</th>
                    <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;
                               color:#6b7280;text-transform:uppercase;letter-spacing:.5px;
                               border-bottom:1px solid #e5e7eb">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deliveryOrders as $do)
                @php
                    $detailRoute = $isOfficer
                        ? route('officer.do.detail', $do->DONumber)
                        : route('vendor.do.detail', $do->DONumber);
                @endphp
                <tr style="border-bottom:1px solid #f3f4f6">
                    <td style="padding:12px 16px;font-weight:600;color:{{ $accent }}">
                        {{ $do->DONumber }}
                    </td>
                    @if($isOfficer)
                    <td style="padding:12px 16px;font-size:12px;color:#374151">
                        {{ $do->vendor->CompanyName ?? '—' }}
                    </td>
                    @endif
                    <td style="padding:12px 16px;color:#374151">{{ $do->PONumber ?? '—' }}</td>
                    <td style="padding:12px 16px;font-size:12px;color:#6b7280">
                        {{ $do->ProjectReference ?? '—' }}
                    </td>
                    <td style="padding:12px 16px;font-size:12px;color:#9ca3af">
                        {{ $do->SubmittedDate
                            ? \Carbon\Carbon::parse($do->SubmittedDate)->timezone('Asia/Kuala_Lumpur')->format('d M Y')
                            : ($do->DOStatus === 'Draft' ? 'Not submitted' : '—') }}
                    </td>
                    <td style="padding:12px 16px">
                        @if($do->DOStatus === 'Approved')
                            <span style="background:#dcfce7;color:#16a34a;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700">Approved</span>
                        @elseif($do->DOStatus === 'Rejected')
                            <span style="background:#fee2e2;color:#991b1b;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700">Rejected</span>
                        @elseif($do->DOStatus === 'Submitted')
                            <span style="background:#dbeafe;color:#1d4ed8;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700">Submitted</span>
                        @elseif($do->DOStatus === 'Under Review')
                            <span style="background:#fef3c7;color:#92400e;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700">Under Review</span>
                        @else
                            <span style="background:#e5e7eb;color:#374151;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700">Draft</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px">
                        <a href="{{ $detailRoute }}"
                           style="color:{{ $accent }};font-size:12px;font-weight:600;text-decoration:none">
                            View →
                        </a>
                        @if($isOfficer && $do->DOStatus === 'Submitted')
                        <a href="{{ route('officer.do.review') }}"
                           style="color:#065f46;font-size:12px;font-weight:600;text-decoration:none;
                                  margin-left:10px;background:#d1fae5;padding:3px 10px;border-radius:6px">
                            Review
                        </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ $isOfficer ? 7 : 6 }}"
                        style="text-align:center;padding:48px;color:#9ca3af">
                        <i class="fa fa-inbox"
                           style="font-size:32px;display:block;margin-bottom:12px;
                                  color:{{ $isOfficer ? '#34d399' : '#93c5fd' }};opacity:.5"></i>
                        <div style="font-weight:600;color:{{ $accent }};margin-bottom:4px">
                            No delivery orders found
                        </div>
                        @if(!$isOfficer && Auth::user()->vendor->isActive())
                        <a href="{{ route('vendor.do.create') }}"
                           style="color:#1d4ed8;font-weight:600;font-size:13px">
                            Submit your first DO →
                        </a>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
