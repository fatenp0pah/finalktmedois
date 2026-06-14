@extends('layouts.app')
@section('title', 'Review Delivery Orders')
@section('content')

<div class="ktm-page-header">
    <div>
        <h2 class="ktm-page-title"><i class="fa fa-clipboard-check me-2" style="color:var(--primary)"></i>Review Delivery Orders</h2>
        <div class="ktm-page-sub">Approve or reject submitted Delivery Orders</div>
    </div>
</div>

@if(session('success'))
<div class="ktm-alert ktm-alert-success mb-4"><i class="fa fa-check-circle mt-1" style="flex-shrink:0"></i><span>{{ session('success') }}</span></div>
@endif
@if(session('error'))
<div class="ktm-alert ktm-alert-danger mb-4"><i class="fa fa-exclamation-circle mt-1" style="flex-shrink:0"></i><span>{{ session('error') }}</span></div>
@endif

<div class="ktm-card">
    <div class="ktm-card-header"><i class="fa fa-list me-2"></i>Delivery Order Review List</div>
    <div class="p-0" style="overflow-x:auto">
        <table class="ktm-table">
            <thead>
                <tr>
                    <th>DO Number</th>
                    <th>Vendor</th>
                    <th>PO No</th>
                    <th>Submitted</th>
                    <th>Status</th>
                    <th>Documents</th>
                    <th>Officer Review</th>
                    <th>Detail</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deliveryOrders as $do)
                <tr>
                    <td style="font-weight:600">{{ $do->DONumber }}</td>
                    <td style="font-size:12px">{{ $do->vendor->CompanyName ?? '—' }}</td>
                    <td>{{ $do->PONumber ?? '—' }}</td>
                    <td style="font-size:12px;color:var(--text-muted)">
                        {{ $do->SubmittedDate ? \Carbon\Carbon::parse($do->SubmittedDate)->format('d M Y') : '—' }}
                    </td>
                    <td>
                        @if($do->DOStatus === 'Approved')     <span class="badge-approved">Approved</span>
                        @elseif($do->DOStatus === 'Rejected')  <span class="badge-rejected">Rejected</span>
                        @elseif($do->DOStatus === 'Submitted') <span class="badge-submitted">Submitted</span>
                        @else <span class="badge-review">Under Review</span>
                        @endif
                    </td>
                    <td style="font-size:12px;color:var(--text-muted)">
                        @if($do->DOFileLink) <div><i class="fa fa-file-pdf me-1"></i>DO uploaded</div> @else <div style="color:#9ca3af">No DO file</div> @endif
                        @if($do->ProofFileLink) <div><i class="fa fa-image me-1"></i>Proof uploaded</div> @else <div style="color:#9ca3af">No proof</div> @endif
                    </td>
                    <td style="min-width:260px">
                        @if(in_array($do->DOStatus, ['Approved', 'Rejected']))
                            <div style="padding:10px;border-radius:8px;font-size:13px;background:{{ $do->DOStatus === 'Approved' ? '#dcfce7' : '#fee2e2' }};color:{{ $do->DOStatus === 'Approved' ? '#166534' : '#991b1b' }}">
                                <strong>{{ $do->DOStatus }}</strong><br>{{ Str::limit($do->Remark, 80) }}
                            </div>
                        @else
                            <form action="{{ route('officer.do.update') }}" method="POST">
                                @csrf
                                <input type="hidden" name="DOID" value="{{ $do->DOID }}">
                                <textarea name="remark" placeholder="Enter remark or rejection reason..." rows="2"
                                    class="form-control ktm-input mb-2" style="font-size:12px"></textarea>
                                <div style="display:flex;gap:8px">
                                    <button type="submit" name="action" value="approve" class="btn-ktm" style="flex:1;padding:8px;font-size:12px">
                                        <i class="fa fa-check me-1"></i>Approve
                                    </button>
                                    <button type="submit" name="action" value="reject" style="flex:1;padding:8px;font-size:12px;background:#dc2626;color:#fff;border:none;border-radius:8px;cursor:pointer;font-weight:600">
                                        <i class="fa fa-times me-1"></i>Reject
                                    </button>
                                </div>
                            </form>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('officer.do.detail', $do->DONumber) }}" style="color:var(--primary);font-weight:600;font-size:13px">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center;padding:32px;color:var(--text-muted)">No Delivery Orders available for review.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
