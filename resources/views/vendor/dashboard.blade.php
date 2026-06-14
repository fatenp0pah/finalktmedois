@extends('layouts.app')
@section('title', 'Vendor Dashboard')
@section('content')

{{-- Restriction banner for Inactive/Deactivated vendors --}}
@if(!$vendor->isActive())
<div style="background:#fef2f2;border-left:5px solid #dc2626;border-radius:10px;padding:14px 18px;margin-bottom:24px;display:flex;align-items:flex-start;gap:12px">
    <i class="fa fa-exclamation-triangle" style="color:#dc2626;margin-top:2px;flex-shrink:0"></i>
    <div>
        <div style="font-weight:700;color:#991b1b;margin-bottom:3px">Account Restricted — {{ $vendor->VendorStatus }}</div>
        <div style="font-size:13px;color:#b91c1c">
            Your vendor account is <strong>{{ $vendor->VendorStatus }}</strong>.
            Submitting Delivery Orders or Invoices is not permitted until your status is updated in KTMB master database.
            Contact KTMB Procurement: <em>procurement@ktm.com.my</em>
        </div>
    </div>
</div>
@endif

{{-- Page header --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px">
    <div style="display:flex;align-items:center;gap:14px">
        <img src="{{ asset('images/R.png') }}" alt="KTM Logo" style="height:40px;width:auto">
        <div>
            <h2 style="margin:0;font-size:20px;font-weight:800;color:#1e3a8a">Vendor Dashboard</h2>
            <div style="font-size:12px;color:#6b7280">{{ $vendor->CompanyName }} · {{ $vendor->VendorNumber }}</div>
        </div>
    </div>
    <div style="display:flex;gap:10px;align-items:center">
        @if($vendor->isActive())
        <a href="{{ route('vendor.do.create') }}" style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;background:#1e3a8a;color:#fff;border-radius:9px;font-size:13px;font-weight:700;text-decoration:none">
            <i class="fa fa-plus"></i> Submit DO
        </a>
        @endif
        <span style="font-size:11px;padding:4px 12px;border-radius:20px;font-weight:700;background:{{ $vendor->isActive() ? '#dbeafe' : '#fee2e2' }};color:{{ $vendor->isActive() ? '#1d4ed8' : '#991b1b' }}">
            <i class="fa fa-circle" style="font-size:8px"></i> {{ $vendor->VendorStatus }}
        </span>
    </div>
</div>

{{-- ── Stat Cards ── --}}
<div class="row g-3 mb-4">
    @foreach([
        ['label'=>'Total Delivery Orders', 'value'=>$totalDOs,    'icon'=>'fa-file-alt',          'bg'=>'#dbeafe','ic'=>'#1d4ed8'],
        ['label'=>'Approved DOs',          'value'=>$approvedDOs, 'icon'=>'fa-clipboard-check',   'bg'=>'#dcfce7','ic'=>'#16a34a'],
        ['label'=>'Total Invoices',        'value'=>$totalInvoices,'icon'=>'fa-file-invoice-dollar','bg'=>'#fef3c7','ic'=>'#d97706'],
        ['label'=>'Paid Invoices',         'value'=>$paidInvoices, 'icon'=>'fa-check-circle',      'bg'=>'#ede9fe','ic'=>'#7c3aed'],
    ] as $card)
    <div class="col-6 col-lg-3">
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:20px;box-shadow:0 1px 4px rgba(0,0,0,0.06)">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
                <div style="width:42px;height:42px;border-radius:10px;background:{{ $card['bg'] }};display:flex;align-items:center;justify-content:center">
                    <i class="fa {{ $card['icon'] }}" style="color:{{ $card['ic'] }};font-size:18px"></i>
                </div>
            </div>
            <div style="font-size:28px;font-weight:800;color:#1e3a8a">{{ $card['value'] }}</div>
            <div style="font-size:12px;color:#6b7280;margin-top:3px">{{ $card['label'] }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- ── Claim Value Summary ── --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-lg-5">
        <div style="background:#1e3a8a;border-radius:14px;padding:24px;color:#fff;height:100%">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:18px">
                <img src="{{ asset('images/R.png') }}" alt="KTM" style="height:28px;filter:brightness(0) invert(1) sepia(1) saturate(5) hue-rotate(180deg)">
                <div style="font-size:14px;font-weight:700">Claim Value Summary</div>
            </div>
            <div style="font-size:32px;font-weight:800;margin-bottom:4px">
                RM {{ number_format($totalInvoicedAmount ?? 0, 2) }}
            </div>
            <div style="font-size:11px;color:rgba(255,255,255,0.6);margin-bottom:20px">Total invoiced amount</div>
            <div style="display:flex;flex-direction:column;gap:12px">
                @foreach([
                    ['Subtotal',  $totalSubtotal ?? 0,  '#fbbf24', '+'],
                    ['Tax (6%)',  $totalTax ?? 0,       '#93c5fd', '+'],
                    ['Discount',  $totalDiscount ?? 0,  '#6ee7b7', '-'],
                    ['Penalty',   $totalPenalty ?? 0,   '#fca5a5', '-'],
                ] as [$lbl,$val,$clr,$sign])
                <div style="display:flex;justify-content:space-between;align-items:center">
                    <div style="display:flex;align-items:center;gap:8px">
                        <div style="width:8px;height:8px;border-radius:50%;background:{{ $clr }}"></div>
                        <span style="font-size:13px;color:rgba(255,255,255,0.8)">{{ $lbl }}</span>
                    </div>
                    <span style="font-size:13px;font-weight:600;color:{{ $clr }}">{{ $sign }} RM {{ number_format($val, 2) }}</span>
                </div>
                @endforeach
                <div style="border-top:1px solid rgba(255,255,255,0.15);padding-top:12px;display:flex;justify-content:space-between">
                    <span style="font-size:13px;font-weight:700">Total Claimed (Paid)</span>
                    <span style="font-size:14px;font-weight:800;color:#fbbf24">RM {{ number_format($totalClaimedAmount ?? 0, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- DO Status Breakdown --}}
    <div class="col-12 col-lg-4">
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:24px;height:100%">
            <div style="font-size:14px;font-weight:700;color:#1e3a8a;margin-bottom:18px;display:flex;align-items:center;gap:8px">
                <i class="fa fa-truck" style="color:#fbbf24"></i> DO Status Breakdown
            </div>
            @foreach([
                ['Submitted',   $submittedDOs,   '#3b82f6'],
                ['Under Review',$underReviewDOs, '#f59e0b'],
                ['Approved',    $approvedDOs,    '#16a34a'],
                ['Rejected',    $rejectedDOs,    '#dc2626'],
            ] as [$lbl,$cnt,$clr])
            @php $pct = $totalDOs > 0 ? ($cnt/$totalDOs)*100 : 0; @endphp
            <div style="margin-bottom:14px">
                <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:5px">
                    <span style="color:#374151;font-weight:600">{{ $lbl }}</span>
                    <span style="color:{{ $clr }};font-weight:700">{{ $cnt }}</span>
                </div>
                <div style="background:#f3f4f6;border-radius:20px;height:7px">
                    <div style="width:{{ $pct }}%;background:{{ $clr }};border-radius:20px;height:7px;transition:width .4s"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="col-12 col-lg-3">
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:24px;height:100%">
            <div style="font-size:14px;font-weight:700;color:#1e3a8a;margin-bottom:16px;display:flex;align-items:center;gap:8px">
                <i class="fa fa-bolt" style="color:#fbbf24"></i> Quick Actions
            </div>
            <div style="display:flex;flex-direction:column;gap:10px">
                @if($vendor->isActive())
                <a href="{{ route('vendor.do.create') }}" style="display:flex;align-items:center;gap:12px;padding:12px;border-radius:10px;background:#eff6ff;text-decoration:none;border:1px solid #bfdbfe">
                    <div style="width:36px;height:36px;border-radius:8px;background:#1e3a8a;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <i class="fa fa-upload" style="color:#fbbf24;font-size:14px"></i>
                    </div>
                    <div>
                        <div style="font-size:13px;font-weight:700;color:#1e3a8a">Submit DO</div>
                        <div style="font-size:11px;color:#6b7280">New delivery order</div>
                    </div>
                </a>
                <a href="{{ route('vendor.invoice.create') }}" style="display:flex;align-items:center;gap:12px;padding:12px;border-radius:10px;background:#fffbeb;text-decoration:none;border:1px solid #fde68a">
                    <div style="width:36px;height:36px;border-radius:8px;background:#d97706;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <i class="fa fa-file-invoice-dollar" style="color:#fff;font-size:14px"></i>
                    </div>
                    <div>
                        <div style="font-size:13px;font-weight:700;color:#92400e">Submit Invoice</div>
                        <div style="font-size:11px;color:#6b7280">Against approved DO</div>
                    </div>
                </a>
                @else
                <div style="padding:12px;border-radius:10px;background:#fef2f2;border:1px solid #fecaca;font-size:12px;color:#991b1b;text-align:center">
                    <i class="fa fa-lock" style="margin-bottom:6px;display:block;font-size:20px"></i>
                    Submissions locked.<br>Account is {{ $vendor->VendorStatus }}.
                </div>
                @endif
                <a href="{{ route('vendor.profile') }}" style="display:flex;align-items:center;gap:12px;padding:12px;border-radius:10px;background:#f9fafb;text-decoration:none;border:1px solid #e5e7eb">
                    <div style="width:36px;height:36px;border-radius:8px;background:#374151;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <i class="fa fa-building" style="color:#fff;font-size:14px"></i>
                    </div>
                    <div>
                        <div style="font-size:13px;font-weight:700;color:#111827">My Profile</div>
                        <div style="font-size:11px;color:#6b7280">Company details & sync</div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ── Recent Delivery Orders Table ── --}}
<div style="background:#fff;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 4px rgba(0,0,0,0.06)">
    <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between">
        <div style="display:flex;align-items:center;gap:8px">
            <div style="width:6px;height:20px;background:#1e3a8a;border-radius:3px"></div>
            <span style="font-size:14px;font-weight:700;color:#1e3a8a">Recent Delivery Orders</span>
        </div>
        <a href="{{ route('vendor.do.dashboard') }}" style="font-size:12px;color:#1d4ed8;font-weight:600;text-decoration:none">View All →</a>
    </div>
    <div style="overflow-x:auto">
        <table style="width:100%;border-collapse:collapse;font-size:13px">
            <thead>
                <tr style="background:#f8fafc">
                    <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;border-bottom:1px solid #e5e7eb">DO Number</th>
                    <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;border-bottom:1px solid #e5e7eb">PO Number</th>
                    <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;border-bottom:1px solid #e5e7eb">Submitted</th>
                    <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;border-bottom:1px solid #e5e7eb">Status</th>
                    <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;border-bottom:1px solid #e5e7eb">Invoice</th>
                    <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;border-bottom:1px solid #e5e7eb">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentDOs ?? [] as $do)
                <tr style="border-bottom:1px solid #f3f4f6">
                    <td style="padding:12px 16px;font-weight:600;color:#1e3a8a">{{ $do->DONumber }}</td>
                    <td style="padding:12px 16px;color:#374151">{{ $do->PONumber ?? '—' }}</td>
                    <td style="padding:12px 16px;color:#9ca3af;font-size:12px">
                        {{ $do->SubmittedDate ? \Carbon\Carbon::parse($do->SubmittedDate)->format('d M Y') : '—' }}
                    </td>
                    <td style="padding:12px 16px">
                        @if($do->DOStatus === 'Approved')
                            <span style="background:#dcfce7;color:#16a34a;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700">Approved</span>
                        @elseif($do->DOStatus === 'Under Review')
                            <span style="background:#fef3c7;color:#92400e;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700">Under Review</span>
                        @elseif($do->DOStatus === 'Submitted')
                            <span style="background:#dbeafe;color:#1d4ed8;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700">Submitted</span>
                        @elseif($do->DOStatus === 'Rejected')
                            <span style="background:#fee2e2;color:#991b1b;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700">Rejected</span>
                        @else
                            <span style="background:#e5e7eb;color:#374151;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700">Draft</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px">
                        @if($do->invoice)
                            <span style="background:#ede9fe;color:#7c3aed;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700">{{ $do->invoice->InvoiceStatus }}</span>
                        @else
                            <span style="color:#d1d5db;font-size:12px">None</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px">
                        <a href="{{ route('vendor.do.detail', $do->DONumber) }}" style="color:#1d4ed8;font-size:12px;font-weight:600;text-decoration:none">View →</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;color:#9ca3af;padding:40px;font-size:13px">
                        <i class="fa fa-inbox" style="font-size:28px;display:block;margin-bottom:10px;opacity:.3"></i>
                        No delivery orders yet.
                        @if($vendor->isActive())
                        <a href="{{ route('vendor.do.create') }}" style="color:#1d4ed8;font-weight:600"> Submit your first DO →</a>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
