<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>KTMeDOIS — @yield('title', 'Dashboard')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
    @livewireStyles
    <style>
        :root {
            --primary:        #1e3a8a;
            --primary-dark:   #1e40af;
            --primary-accent: #fbbf24;
            --sidebar-bg:     #0f2044;
            --sidebar-text:   #e5e7eb;
            --sidebar-hover:  rgba(251,191,36,0.12);
            --sidebar-active: #fbbf24;
            --body-bg:        #f0f4f8;
            --card-bg:        #ffffff;
            --card-border:    #e2e8f0;
            --text:           #0f172a;
            --text-muted:     #64748b;
            --text-light:     #94a3b8;
            --stat-icon-bg:   #dbeafe;
        }
        *{box-sizing:border-box;}
        body{margin:0;font-family:'Segoe UI',Arial,sans-serif;background:var(--body-bg);color:var(--text);min-height:100vh;display:flex;}

        /* Sidebar */
        .ktm-sidebar{width:260px;height:100vh;background:var(--sidebar-bg);display:flex;flex-direction:column;position:fixed;top:0;left:0;z-index:100;overflow:hidden;}
        .ktm-sidebar-header{padding:20px 16px 16px;text-align:center;flex-shrink:0;}
        .ktm-sidebar-title{color:#fff;font-size:18px;font-weight:800;margin:0;}
        .ktm-sidebar-sub{color:rgba(255,255,255,0.7);font-size:10px;margin-top:3px;}

        /* Scrollable nav */
        .ktm-nav-scroll{flex:1;overflow-y:auto;overflow-x:hidden;padding-bottom:8px;}
        .ktm-nav-scroll::-webkit-scrollbar{width:4px;}
        .ktm-nav-scroll::-webkit-scrollbar-track{background:transparent;}
        .ktm-nav-scroll::-webkit-scrollbar-thumb{background:rgba(255,255,255,0.1);border-radius:4px;}

        .ktm-user-box{margin:14px 12px;background:rgba(255,255,255,0.07);border-radius:10px;padding:12px;font-size:12px;color:var(--sidebar-text);}
        .role-badge{display:inline-block;padding:2px 10px;border-radius:20px;font-size:10px;font-weight:700;margin-top:4px;}
        .role-vendor{background:rgba(251,191,36,0.2);color:#fbbf24;font-weight:700;}
        .role-officer{background:rgba(251,191,36,0.2);color:#fbbf24;}

        .ktm-nav-divider{height:1px;background:rgba(255,255,255,0.06);margin:6px 16px;}
        .ktm-nav-link{display:flex;align-items:center;gap:10px;color:var(--sidebar-text);text-decoration:none;padding:10px 16px;margin:2px 8px;border-radius:8px;font-size:13px;font-weight:500;transition:all .2s;}
        .ktm-nav-link:hover{background:var(--sidebar-hover);color:#fbbf24;font-weight:700;}
        .ktm-nav-link.active{background:var(--sidebar-active);color:#1e3a8a;font-weight:700;}
        .ktm-nav-link i{width:16px;text-align:center;font-size:13px;}

        /* Logout pinned at bottom */
        .ktm-sidebar-footer{flex-shrink:0;padding:12px;background:var(--sidebar-bg);border-top:1px solid rgba(255,255,255,0.08);}
        .ktm-logout{display:flex;align-items:center;justify-content:center;gap:10px;color:#fca5a5;padding:10px 16px;border-radius:8px;font-size:13px;font-weight:600;transition:all .2s;border:1px solid rgba(220,38,38,0.3);width:100%;background:none;cursor:pointer;}
        .ktm-logout:hover{background:rgba(220,38,38,0.15);color:#f87171;}

        /* Main */
        .ktm-main{margin-left:260px;flex:1;display:flex;flex-direction:column;min-height:100vh;}
        .ktm-topbar{padding:14px 28px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:50;}
        .ktm-topbar-title{color:#fff;font-size:15px;font-weight:700;}
        .ktm-topbar-right{display:flex;align-items:center;gap:12px;}
        .ktm-topbar-user{color:rgba(255,255,255,0.9);font-size:12px;display:flex;align-items:center;gap:6px;}
        .ktm-content{padding:28px;flex:1;}

        /* Cards */
        .ktm-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:14px;box-shadow:0 2px 8px rgba(0,0,0,0.05);}
        .ktm-card-header{padding:14px 20px;border-bottom:1px solid var(--card-border);font-size:14px;font-weight:700;color:var(--text);display:flex;align-items:center;gap:8px;}
        .ktm-card-body{padding:20px;}

        /* Page header */
        .ktm-page-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;}
        .ktm-page-title{font-size:22px;font-weight:800;color:var(--text);margin:0;}
        .ktm-page-sub{font-size:12px;color:var(--text-muted);margin-top:3px;}

        /* Buttons */
        .btn-ktm{display:inline-flex;align-items:center;gap:6px;padding:9px 18px;background:linear-gradient(135deg,#1e3a8a,#1e40af);color:#fff;border:none;border-radius:9px;font-size:13px;font-weight:700;cursor:pointer;text-decoration:none;transition:opacity .2s;}
        .btn-ktm:hover{opacity:.88;color:#fff;}
        .btn-ktm-outline{display:inline-flex;align-items:center;gap:6px;padding:8px 18px;background:transparent;color:var(--primary);border:1.5px solid var(--primary);border-radius:9px;font-size:13px;font-weight:700;cursor:pointer;text-decoration:none;transition:all .2s;}
        .btn-ktm-outline:hover{background:var(--primary);color:#fff;}

        /* Inputs */
        .ktm-label{display:block;font-size:12px;font-weight:700;color:var(--text-muted);margin-bottom:6px;text-transform:uppercase;letter-spacing:.5px;}
        .ktm-input{border:1.5px solid var(--card-border)!important;border-radius:9px!important;padding:10px 14px!important;font-size:13px!important;background:var(--body-bg)!important;color:var(--text)!important;transition:border-color .2s!important;}
        .ktm-input:focus{border-color:var(--primary)!important;box-shadow:0 0 0 3px rgba(30,58,138,.12)!important;outline:none!important;}

        /* Table */
        .ktm-table{width:100%;border-collapse:collapse;font-size:13px;}
        .ktm-table th{background:var(--body-bg);color:var(--text-muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;padding:12px 16px;border-bottom:1px solid var(--card-border);text-align:left;}
        .ktm-table td{padding:12px 16px;border-bottom:1px solid var(--card-border);color:var(--text);vertical-align:middle;}
        .ktm-table tr:last-child td{border-bottom:none;}
        .ktm-table tr:hover td{background:var(--body-bg);}

        /* Badges */
        .badge-approved{background:#dcfce7;color:#166534;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700;display:inline-block;}
        .badge-rejected{background:#fee2e2;color:#991b1b;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700;display:inline-block;}
        .badge-submitted{background:#dbeafe;color:#1d4ed8;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700;display:inline-block;}
        .badge-review{background:#fef3c7;color:#1e40af;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700;display:inline-block;}
        .badge-active{background:#dcfce7;color:#166534;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700;display:inline-block;}
        .badge-inactive{background:#e5e7eb;color:#374151;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700;display:inline-block;}
        .badge-deactivated{background:#fee2e2;color:#991b1b;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700;display:inline-block;}

        /* Alerts */
        .ktm-alert{display:flex;align-items:flex-start;gap:10px;padding:12px 16px;border-radius:10px;font-size:13px;}
        .ktm-alert-info{background:#eff6ff;color:#1e40af;border-left:4px solid #3b82f6;}
        .ktm-alert-success{background:#dcfce7;color:#166534;border-left:4px solid #22c55e;}
        .ktm-alert-warning{background:#dbeafe;color:#1e40af;border-left:4px solid #1e3a8a;}
        .ktm-alert-danger{background:#fee2e2;color:#991b1b;border-left:4px solid #dc2626;}

        /* Profile field */
        .profile-field-val{display:flex;align-items:center;gap:8px;font-size:14px;font-weight:600;color:var(--text);background:var(--body-bg);border:1px solid var(--card-border);border-radius:9px;padding:10px 14px;}

        @media(max-width:768px){.ktm-sidebar{transform:translateX(-260px);}.ktm-main{margin-left:0;}.ktm-content{padding:16px;}}
        @media print{.ktm-sidebar,.ktm-topbar{display:none!important;}.ktm-main{margin-left:0;}}
    </style>
    @stack('styles')
</head>
<body>

<aside class="ktm-sidebar">

    {{-- Header --}}
    <div class="ktm-sidebar-header" style="{{ Auth::check() && Auth::user()->isOfficer() ? 'background:linear-gradient(135deg,#064e3b 0%,#065f46 60%,#047857 100%)' : 'background:linear-gradient(135deg,#1e3a8a 0%,#1e40af 60%,#1d4ed8 100%)' }}">
        <img src="{{ asset('images/R.png') }}" alt="KTM" style="height:44px;width:auto;margin-bottom:8px">
        <div class="ktm-sidebar-title">KTMeDOIS</div>
        <div class="ktm-sidebar-sub">Electronic DO & Invoice System</div>
    </div>

    {{-- Nav links — scrollable --}}
    <div class="ktm-nav-scroll">

        @auth
        <div class="ktm-user-box">
            <div style="font-size:11px;color:rgba(255,255,255,0.5);margin-bottom:4px">Logged in as</div>
            <div style="font-weight:700;font-size:13px;color:#fff">{{ Auth::user()->Username }}</div>
            <span class="role-badge {{ Auth::user()->isVendor() ? 'role-vendor' : 'role-officer' }}">
                {{ Auth::user()->UserRole }}
            </span>
            @if(Auth::user()->isVendor() && Auth::user()->vendor)
                <div style="font-size:10px;color:rgba(255,255,255,0.4);margin-top:4px">{{ Auth::user()->vendor->VendorNumber }}</div>
            @endif
        </div>
        @endauth

        @auth
        @if(Auth::user()->isVendor())
            {{-- Vendor nav --}}
            <a href="{{ route('vendor.dashboard') }}" class="ktm-nav-link {{ request()->routeIs('vendor.dashboard') ? 'active' : '' }}">
                <i class="fa fa-home"></i> Dashboard
            </a>
            <a href="{{ route('vendor.profile') }}" class="ktm-nav-link {{ request()->routeIs('vendor.profile') ? 'active' : '' }}">
                <i class="fa fa-id-card"></i> My Profile
            </a>

            <div class="ktm-nav-divider"></div>

            <a href="{{ route('vendor.do.dashboard') }}" class="ktm-nav-link {{ request()->routeIs('vendor.do.dashboard') ? 'active' : '' }}">
                <i class="fa fa-truck"></i> DO Dashboard
            </a>
            @if(Auth::user()->vendor && Auth::user()->vendor->isActive())
            <a href="{{ route('vendor.do.create') }}" class="ktm-nav-link {{ request()->routeIs('vendor.do.create') ? 'active' : '' }}">
                <i class="fa fa-plus-circle"></i> Create DO
            </a>
            @endif
            <a href="{{ route('vendor.do.status') }}" class="ktm-nav-link {{ request()->routeIs('vendor.do.status') ? 'active' : '' }}">
                <i class="fa fa-tasks"></i> DO Status
            </a>
            <a href="{{ route('vendor.do.report') }}" class="ktm-nav-link {{ request()->routeIs('vendor.do.report') ? 'active' : '' }}">
                <i class="fa fa-chart-bar"></i> DO Report
            </a>
            <a href="{{ route('vendor.do.notifications') }}" class="ktm-nav-link {{ request()->routeIs('vendor.do.notifications') ? 'active' : '' }}">
                <i class="fa fa-bell"></i> Notifications
            </a>

            <div class="ktm-nav-divider"></div>

            @if(Auth::user()->vendor && Auth::user()->vendor->isActive())
            <a href="{{ route('vendor.invoice.index') }}" class="ktm-nav-link {{ request()->routeIs('vendor.invoice.index') ? 'active' : '' }}">
                <i class="fa fa-file-invoice"></i> My Invoices
            </a>
            <a href="{{ route('vendor.invoice.create') }}" class="ktm-nav-link {{ request()->routeIs('vendor.invoice.create') ? 'active' : '' }}">
                <i class="fa fa-plus-circle"></i> Submit Invoice
            </a>
            @else
            <div class="ktm-nav-link" style="opacity:.4;cursor:not-allowed">
                <i class="fa fa-lock"></i> Invoices (Inactive)
            </div>
            @endif
        @endif

        @if(Auth::user()->isOfficer())
            {{-- Officer nav --}}
            <a href="{{ route('officer.dashboard') }}" class="ktm-nav-link {{ request()->routeIs('officer.dashboard') ? 'active' : '' }}">
                <i class="fa fa-home"></i> Dashboard
            </a>
            <a href="{{ route('officer.do.review') }}" class="ktm-nav-link {{ request()->routeIs('officer.do.review') ? 'active' : '' }}">
                <i class="fa fa-clipboard-check"></i> Review DOs
            </a>
            <a href="{{ route('officer.do.status') }}" class="ktm-nav-link {{ request()->routeIs('officer.do.status') ? 'active' : '' }}">
                <i class="fa fa-tasks"></i> DO Status
            </a>
            <a href="{{ route('officer.do.report') }}" class="ktm-nav-link {{ request()->routeIs('officer.do.report') ? 'active' : '' }}">
                <i class="fa fa-chart-bar"></i> DO Report
            </a>
            <a href="{{ route('officer.notifications') }}" class="ktm-nav-link {{ request()->routeIs('officer.notifications') ? 'active' : '' }}">
                <i class="fa fa-bell"></i> Notifications
            </a>

            <div class="ktm-nav-divider"></div>

            <a href="{{ route('officer.invoices') }}" class="ktm-nav-link {{ request()->routeIs('officer.invoices') ? 'active' : '' }}">
                <i class="fa fa-file-invoice"></i> Invoice List
            </a>
            <a href="{{ route('officer.audit') }}" class="ktm-nav-link {{ request()->routeIs('officer.audit') ? 'active' : '' }}">
                <i class="fa fa-history"></i> Audit Log
            </a>
        @endif
        @endauth

    </div>

    {{-- Logout — always pinned at bottom --}}
    <div class="ktm-sidebar-footer">
        @auth
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="ktm-logout">
                <i class="fa fa-sign-out-alt"></i> Logout
            </button>
        </form>
        @endauth
    </div>

</aside>

<div class="ktm-main">
    <div class="ktm-topbar" style="{{ Auth::check() && Auth::user()->isOfficer() ? 'background:linear-gradient(135deg,#064e3b 0%,#065f46 60%,#047857 100%)' : 'background:linear-gradient(135deg,#1e3a8a 0%,#1e40af 60%,#1d4ed8 100%)' }}">
        <div class="ktm-topbar-title" style="display:flex;align-items:center;gap:10px">
            <img src="{{ asset('images/R.png') }}" alt="KTM" style="height:28px;filter:brightness(0) invert(1)">
            <span>KTMeDOIS</span>
        </div>
        <div class="ktm-topbar-right">
            @auth
            <div class="ktm-topbar-user">
                <i class="fa fa-user-circle"></i>
                {{ Auth::user()->Username }}
                <span style="opacity:.6">|</span>
                <span style="opacity:.8">{{ Auth::user()->UserRole }}</span>
            </div>
            @if(Auth::user()->isVendor() && Auth::user()->vendor)
            <div style="font-size:11px;color:rgba(255,255,255,0.7)">
                @if(Auth::user()->vendor->isActive())
                    <span style="color:#86efac"><i class="fa fa-circle" style="font-size:8px"></i> Active</span>
                @else
                    <span style="color:#fca5a5"><i class="fa fa-circle" style="font-size:8px"></i> {{ Auth::user()->vendor->VendorStatus }}</span>
                @endif
            </div>
            @endif
            @endauth
        </div>
    </div>

    <div class="ktm-content">
        @yield('content')
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
@livewireScripts
@stack('scripts')

</body>
</html>
