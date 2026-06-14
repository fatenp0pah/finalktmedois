{{-- notifications.blade.php --}}
@extends('layouts.app')
@section('title', 'Notifications')
@section('content')
<div class="ktm-page-header">
    <div>
        <h2 class="ktm-page-title"><i class="fa fa-bell me-2" style="color:var(--primary)"></i>Notifications</h2>
        <div class="ktm-page-sub">Delivery Order updates and system alerts</div>
    </div>
    <span style="font-size:13px;color:var(--text-muted)">{{ $notifications->count() }} notification(s)</span>
</div>

<div class="ktm-card">
    <div class="ktm-card-header"><i class="fa fa-list me-2"></i>Notification List</div>
    <div class="ktm-card-body">
        @forelse($notifications as $n)
        @php
            $msg = $n->NotificationMessage;
            $isRead = $n->NotificationStatus === 'Read';
            preg_match('/DO-\d{4}-\d{3}/', $msg, $matches);
            $doNo = $matches[0] ?? null;
        @endphp
        <div style="display:flex;gap:14px;padding:16px;border-radius:12px;border:1px solid var(--card-border);margin-bottom:12px;background:{{ $isRead ? 'var(--body-bg)' : 'var(--stat-icon-bg)' }}">
            <div style="width:40px;height:40px;border-radius:50%;background:var(--primary);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="fa fa-bell" style="color:#fff;font-size:16px"></i>
            </div>
            <div style="flex:1">
                <div style="font-size:13px;font-weight:600;color:var(--text);margin-bottom:4px">{{ $msg }}</div>
                <div style="font-size:11px;color:var(--text-muted)">
                    {{ \Carbon\Carbon::parse($n->CreatedDate)->format('d M Y, H:i') }}
                    — {{ \Carbon\Carbon::parse($n->CreatedDate)->diffForHumans() }}
                </div>
                @if($doNo)
                @php $detailRoute = Auth::user()->isVendor() ? route('vendor.do.detail', $doNo) : route('officer.do.detail', $doNo); @endphp
                <a href="{{ $detailRoute }}" style="font-size:12px;color:var(--primary);font-weight:600;margin-top:6px;display:inline-block">
                    View {{ $doNo }} →
                </a>
                @endif
            </div>
            @if(!$isRead)
            <div style="width:8px;height:8px;border-radius:50%;background:var(--primary);flex-shrink:0;margin-top:4px"></div>
            @endif
        </div>
        @empty
        <div style="text-align:center;padding:48px;color:var(--text-muted)">
            <i class="fa fa-bell-slash" style="font-size:36px;display:block;margin-bottom:16px;opacity:.3"></i>
            No notifications yet.
        </div>
        @endforelse
    </div>
</div>
@endsection
