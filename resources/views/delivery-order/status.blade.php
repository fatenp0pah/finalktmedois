{{-- status.blade.php --}}
@extends('layouts.app')
@section('title', 'DO Status Tracking')
@section('content')
<div class="ktm-page-header">
    <div>
        <h2 class="ktm-page-title"><i class="fa fa-tasks me-2" style="color:var(--primary)"></i>DO Status Tracking</h2>
        <div class="ktm-page-sub">Track the progress of all Delivery Orders</div>
    </div>
</div>
<div class="ktm-card">
    <div class="ktm-card-header"><i class="fa fa-list me-2"></i>Status Tracking List</div>
    <div class="p-0" style="overflow-x:auto">
        <table class="ktm-table">
            <thead>
                <tr>
                    <th>DO Number</th>
                    @if(Auth::user()->isOfficer())<th>Vendor</th>@endif
                    <th>PO Number</th>
                    <th>Submitted</th>
                    <th>Status</th>
                    <th>Progress</th>
                    <th>Remark</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deliveryOrders as $do)
                @php
                    $s = $do->DOStatus;
                    $steps = ['Draft','Submitted','Under Review','Final'];
                    $activeIdx = match($s) { 'Draft'=>0, 'Submitted'=>1, 'Under Review'=>2, 'Approved','Rejected'=>3, default=>0 };
                @endphp
                <tr>
                    <td style="font-weight:600">{{ $do->DONumber }}</td>
                    @if(Auth::user()->isOfficer())<td style="font-size:12px">{{ $do->vendor->CompanyName ?? '—' }}</td>@endif
                    <td>{{ $do->PONumber ?? '—' }}</td>
                    <td style="font-size:12px;color:var(--text-muted)">
                        {{ $do->SubmittedDate ? \Carbon\Carbon::parse($do->SubmittedDate)->format('d M Y') : '—' }}
                    </td>
                    <td>
                        @if($s === 'Approved')     <span class="badge-approved">Approved</span>
                        @elseif($s === 'Rejected')  <span class="badge-rejected">Rejected</span>
                        @elseif($s === 'Submitted') <span class="badge-submitted">Submitted</span>
                        @elseif($s === 'Under Review') <span class="badge-review">Under Review</span>
                        @else <span class="badge-inactive">Draft</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:4px;font-size:11px">
                            @foreach(['Draft','Submitted','Review','Final'] as $i => $step)
                                @php
                                    $isActive = $i <= $activeIdx;
                                    $isFinal = $step === 'Final';
                                    $finalColor = $s === 'Approved' ? '#16a34a' : ($s === 'Rejected' ? '#dc2626' : '#9ca3af');
                                    $bg = $isFinal ? $finalColor : ($isActive ? 'var(--primary)' : '#e5e7eb');
                                    $color = $isActive || $isFinal ? '#fff' : '#6b7280';
                                    $label = $isFinal ? ($s === 'Approved' ? 'Approved' : ($s === 'Rejected' ? 'Rejected' : 'Final')) : $step;
                                @endphp
                                <span style="background:{{ $bg }};color:{{ $color }};padding:3px 7px;border-radius:20px;white-space:nowrap;font-weight:600">{{ $label }}</span>
                                @if(!$loop->last)<span style="color:#9ca3af">→</span>@endif
                            @endforeach
                        </div>
                    </td>
                    <td style="font-size:12px;color:var(--text-muted)">{{ Str::limit($do->Remark, 60) }}</td>
                </tr>
                @empty
                <tr><td colspan="{{ Auth::user()->isOfficer() ? 7 : 6 }}" style="text-align:center;padding:32px;color:var(--text-muted)">No delivery orders found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
