@extends('layouts.app')
@section('title', 'My Profile')
@section('content')

{{-- Success / Error flash --}}
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

{{-- Page header --}}
<div class="ktm-page-header">
    <div style="display:flex;align-items:center;gap:14px">
        <img src="{{ asset('images/R.png') }}" alt="KTM" style="height:36px;width:auto">
        <div>
            <h2 class="ktm-page-title" style="color:#1e3a8a">Vendor Profile</h2>
            <div class="ktm-page-sub">Read-only — retrieved from KTMB master database</div>
        </div>
    </div>
    <form method="POST" action="{{ route('vendor.sync') }}">
        @csrf
        <button type="submit" class="btn-ktm"
            style="background:linear-gradient(135deg,#1e3a8a,#1e40af)"
            onclick="this.innerHTML='<i class=\'fa fa-spinner fa-spin\'></i> Syncing...';this.disabled=true;this.form.submit();">
            <i class="fa fa-sync"></i> Sync from KTMB
        </button>
    </form>
</div>

<div class="row g-4">

    {{-- ── Left: Company Info ── --}}
    <div class="col-lg-8">
        <div class="ktm-card">
            <div class="ktm-card-header" style="background:#1e3a8a;color:#fff;border-radius:13px 13px 0 0">
                <i class="fa fa-building me-2"></i>Company Information
            </div>
            <div class="ktm-card-body">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="ktm-label">Vendor Number</label>
                        <div class="profile-field-val">
                            <i class="fa fa-hashtag" style="color:#1e3a8a;font-size:13px"></i>
                            {{ $vendor->VendorNumber ?? '—' }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="ktm-label">Company Name</label>
                        <div class="profile-field-val">
                            <i class="fa fa-building" style="color:#1e3a8a;font-size:13px"></i>
                            {{ $vendor->CompanyName ?? '—' }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="ktm-label">Reference Number (SSM)</label>
                        <div class="profile-field-val">
                            <i class="fa fa-file-alt" style="color:#1e3a8a;font-size:13px"></i>
                            {{ $vendor->RefNumber ?? '—' }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="ktm-label">Contact Person</label>
                        <div class="profile-field-val">
                            <i class="fa fa-user" style="color:#1e3a8a;font-size:13px"></i>
                            {{ $vendor->ContactPerson ?? '—' }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="ktm-label">Email Address</label>
                        <div class="profile-field-val">
                            <i class="fa fa-envelope" style="color:#1e3a8a;font-size:13px"></i>
                            {{ $vendor->VendorEmail ?? '—' }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="ktm-label">Contact Number</label>
                        <div class="profile-field-val">
                            <i class="fa fa-phone" style="color:#1e3a8a;font-size:13px"></i>
                            {{ $vendor->VendorContactNum ?? '—' }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="ktm-label">Registration Expiry Date</label>
                        <div class="profile-field-val">
                            <i class="fa fa-calendar-alt" style="color:#1e3a8a;font-size:13px"></i>
                            {{ $vendor->ExpiredDate
                                ? \Carbon\Carbon::parse($vendor->ExpiredDate)->format('d M Y')
                                : '—' }}
                            @if($vendor->ExpiredDate && $vendor->ExpiredDate < now()->toDateString())
                                <span class="badge-rejected ms-2" style="font-size:10px">Expired</span>
                            @elseif($vendor->ExpiredDate && \Carbon\Carbon::parse($vendor->ExpiredDate)->diffInDays(now()) <= 30)
                                <span class="badge-inactive ms-2" style="font-size:10px">Expiring Soon</span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="ktm-label">Vendor Status</label>
                        <div class="profile-field-val">
                            @if($vendor->VendorStatus === 'Active')
                                <span class="badge-active">
                                    <i class="fa fa-circle" style="font-size:7px"></i> Active
                                </span>
                            @elseif($vendor->VendorStatus === 'Inactive')
                                <span class="badge-inactive">
                                    <i class="fa fa-pause-circle me-1"></i>Inactive
                                </span>
                            @else
                                <span class="badge-deactivated">
                                    <i class="fa fa-ban me-1"></i>Deactivated
                                </span>
                            @endif
                        </div>
                    </div>

                </div>

                {{-- Read-only notice --}}
                <div class="ktm-alert ktm-alert-info mt-4 mb-0">
                    <i class="fa fa-lock mt-1" style="flex-shrink:0;color:#1e3a8a"></i>
                    <span>
                        <strong>Read-Only:</strong> Vendor information is retrieved directly from the
                        KTMB master database and cannot be edited here. Contact KTMB Procurement at
                        <em>procurement@ktm.com.my</em> to update your records.
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Right: Sync Status ── --}}
    <div class="col-lg-4">
        <div class="ktm-card h-100">
            <div class="ktm-card-header" style="background:#1e3a8a;color:#fff;border-radius:13px 13px 0 0">
                <i class="fa fa-database me-2"></i>KTMB Sync Status
            </div>
            <div class="ktm-card-body">

                {{-- Sync status icon --}}
                <div class="text-center mb-3">
                    <div style="width:70px;height:70px;border-radius:50%;
                                background:#dbeafe;border:3px solid #1e3a8a;
                                display:inline-flex;align-items:center;justify-content:center;
                                margin-bottom:12px">
                        <i class="fa fa-sync-alt fa-2x" style="color:#1e3a8a"></i>
                    </div>
                    <div style="font-size:11px;color:var(--text-muted);margin-bottom:6px">
                        Last synchronised from KTMB master database
                    </div>

                    {{-- This is the real-time value — updates on every page load after sync --}}
                    <div style="font-size:16px;font-weight:800;color:#1e3a8a" id="last-sync-date">
                       {{ $vendor->LastSyncDate
    ? \Carbon\Carbon::parse($vendor->LastSyncDate)->timezone('Asia/Kuala_Lumpur')->format('d M Y, H:i:s')
    : 'Never synced' }}
                    </div>

                    @if($vendor->LastSyncDate)
                    <div style="font-size:11px;color:var(--text-muted);margin-top:4px" id="last-sync-ago">
                       {{ \Carbon\Carbon::parse($vendor->LastSyncDate)->timezone('Asia/Kuala_Lumpur')->diffForHumans() }}
                    </div>
                    @endif
                </div>

                {{-- Info box --}}
                <div style="background:#eff6ff;border:1px solid #bfdbfe;
                            border-radius:10px;padding:12px 14px;margin-bottom:12px">
                    <div style="font-size:11px;color:#1e40af;line-height:1.6">
                        <i class="fa fa-info-circle me-1"></i>
                        Data is automatically retrieved from KTMB master database on every login.
                        Click <strong>Sync from KTMB</strong> at the top to manually refresh.
                    </div>
                </div>

                {{-- Total API transactions count --}}
                <div style="display:flex;align-items:center;justify-content:space-between;
                            background:#f8fafc;border:1px solid #e2e8f0;
                            border-radius:10px;padding:14px 16px">
                    <div>
                        <div style="font-size:11px;color:var(--text-muted)">Total API Transactions</div>
                        <div style="font-size:10px;color:var(--text-light);margin-top:2px">All time</div>
                    </div>
                    <div style="font-size:26px;font-weight:800;color:#1e3a8a">
                        {{ $apiLogs->count() }}
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

{{-- ── API Transaction Log ── --}}
<div class="ktm-card mt-4">
    <div class="ktm-card-header d-flex justify-content-between align-items-center"
         style="background:#1e3a8a;color:#fff;border-radius:13px 13px 0 0">
        <span>
            <i class="fa fa-history me-2"></i>API Transaction Log
        </span>
        <span style="font-size:11px;color:rgba(255,255,255,0.6);font-weight:400">
            KTMB master database communications — auto-updated on sync
        </span>
    </div>

    @if($apiLogs->count())
    <div style="overflow-x:auto">
        <table class="ktm-table">
            <thead>
                <tr>
                    <th style="width:5%">#</th>
                    <th style="width:30%">API Action</th>
                    <th style="width:15%">Status</th>
                    <th style="width:30%">Date &amp; Time</th>
                    <th style="width:20%">Time Ago</th>
                </tr>
            </thead>
            <tbody>
                @foreach($apiLogs as $index => $log)
                <tr>
                    <td style="color:var(--text-muted);font-size:11px">{{ $index + 1 }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px">
                            @if($log->APIAction === 'SyncVendor')
                                <div style="width:28px;height:28px;border-radius:6px;background:#dbeafe;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                    <i class="fa fa-sync" style="color:#1e3a8a;font-size:12px"></i>
                                </div>
                            @elseif($log->APIAction === 'RetrieveVendor')
                                <div style="width:28px;height:28px;border-radius:6px;background:#dcfce7;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                    <i class="fa fa-download" style="color:#16a34a;font-size:12px"></i>
                                </div>
                            @else
                                <div style="width:28px;height:28px;border-radius:6px;background:#fef3c7;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                    <i class="fa fa-search" style="color:#92400e;font-size:12px"></i>
                                </div>
                            @endif
                            <span style="font-size:13px;font-weight:600;color:var(--text)">
                                {{ $log->APIAction }}
                            </span>
                        </div>
                    </td>
                    <td>
                        @if($log->APIStatus === 'Success')
                            <span class="badge-approved">
                                <i class="fa fa-check me-1" style="font-size:9px"></i>{{ $log->APIStatus }}
                            </span>
                        @else
                            <span class="badge-rejected">
                                <i class="fa fa-times me-1" style="font-size:9px"></i>{{ $log->APIStatus }}
                            </span>
                        @endif
                    </td>
                    <td style="font-size:12px;color:var(--text-muted)">
                        {{ $log->LogDate ? $log->LogDate->format('d M Y, H:i:s') : '—' }}
                    </td>
                    <td style="font-size:11px;color:var(--text-light)">
                        {{ $log->LogDate ? $log->LogDate->diffForHumans() : '—' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @else
    <div class="ktm-card-body">
        <div style="text-align:center;padding:32px 0;color:var(--text-muted);font-size:13px">
            <i class="fa fa-database" style="font-size:32px;margin-bottom:12px;display:block;color:#bfdbfe"></i>
            <div style="font-weight:600;margin-bottom:4px">No API transactions yet</div>
            <div style="font-size:12px">Click <strong>Sync from KTMB</strong> above to record your first transaction.</div>
        </div>
    </div>
    @endif
</div>

@push('styles')
<style>
    .profile-field-val {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        font-weight: 600;
        color: var(--text);
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 9px;
        padding: 10px 14px;
        transition: border-color .2s;
    }
    .profile-field-val:hover {
        border-color: #1e3a8a;
    }
    .ktm-card-header {
        border-radius: 13px 13px 0 0;
    }
</style>
@endpush

@endsection
