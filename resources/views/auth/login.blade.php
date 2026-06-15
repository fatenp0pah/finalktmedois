<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KTMeDOIS — Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            min-height: 100vh;
            background: #0f2044;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* KTM blue diagonal background stripes */
        body::before {
            content: '';
            position: fixed;
            top: -50%; left: -50%;
            width: 200%; height: 200%;
            background: repeating-linear-gradient(
                45deg,
                transparent,
                transparent 60px,
                rgba(251,191,36,0.03) 60px,
                rgba(251,191,36,0.03) 120px
            );
            pointer-events: none;
        }

        /* Yellow accent bar top */
        body::after {
            content: '';
            position: fixed;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, #fbbf24, #f59e0b, #fbbf24);
        }

        .login-wrap {
            width: 100%;
            max-width: 480px;
            padding: 20px;
            position: relative;
            z-index: 1;
        }

        /* KTM logo top */
        .ktm-brand {
            text-align: center;
            margin-bottom: 24px;
        }

        .ktm-brand img {
            height: 52px;
            width: auto;
            filter: brightness(0) invert(1);
            margin-bottom: 10px;
        }

        .ktm-brand-name {
            color: #fbbf24;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .login-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 24px 80px rgba(0,0,0,0.4);
            overflow: hidden;
            border: 1px solid rgba(251,191,36,0.2);
        }

        /* Blue header with KTM logo */
        .login-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #1d4ed8 100%);
            padding: 30px 30px 24px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        /* Yellow stripe in header */
        .login-header::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: #fbbf24;
        }

        .login-header::after {
            content: '';
            position: absolute;
            bottom: -30px; right: -30px;
            width: 100px; height: 100px;
            background: rgba(251,191,36,0.08);
            border-radius: 50%;
        }

        .login-logo-wrap {
            margin-bottom: 14px;
        }

        .login-logo-wrap img {
            height: 52px;
            width: auto;
            filter: brightness(0) invert(1);
        }

        .login-title {
            color: #fff;
            font-size: 24px;
            font-weight: 800;
            margin-bottom: 4px;
            letter-spacing: 1px;
        }

        .login-title span {
            color: #fbbf24;
        }

        .login-sub {
            color: rgba(255,255,255,0.7);
            font-size: 11px;
            letter-spacing: .5px;
        }

        /* Yellow divider */
        .login-divider {
            height: 3px;
            background: linear-gradient(90deg, #1e3a8a, #fbbf24, #1e3a8a);
        }

        .login-body { padding: 28px 30px; }

        .form-group { margin-bottom: 18px; }

        label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: #1e3a8a;
            margin-bottom: 7px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #1e3a8a;
            font-size: 14px;
        }

        input[type=email], input[type=password], input[type=text] {
            width: 100%;
            padding: 12px 14px 12px 42px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            outline: none;
            background: #f8fafc;
            color: #0f172a;
            transition: all .2s;
        }

        input:focus {
            border-color: #1e3a8a;
            box-shadow: 0 0 0 3px rgba(30,58,138,.1);
            background: #fff;
        }

        .show-pw {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: #64748b;
            margin-top: 8px;
            cursor: pointer;
            user-select: none;
        }

        .show-pw input[type=checkbox] {
            width: 14px;
            height: 14px;
            padding: 0;
            accent-color: #1e3a8a;
        }

        .btn-login {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #1e3a8a, #1e40af);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 6px;
            transition: all .2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            letter-spacing: .5px;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #1e40af, #1d4ed8);
            box-shadow: 0 4px 16px rgba(30,58,138,.3);
            transform: translateY(-1px);
        }

        .btn-login .btn-arrow {
            background: rgba(255,255,255,0.2);
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
        }

        /* Alerts */
        .alert {
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 16px;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }
        .alert-error { background: #fee2e2; color: #991b1b; border-left: 4px solid #dc2626; }
        .alert-success { background: #dcfce7; color: #166534; border-left: 4px solid #22c55e; }

        /* Demo credentials */
        .credentials-hint {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-left: 4px solid #1e3a8a;
            border-radius: 10px;
            padding: 12px 14px;
            margin-bottom: 20px;
            font-size: 12px;
            color: #1e40af;
        }

        .credentials-hint .hint-title {
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 8px;
            color: #1e3a8a;
        }

        .cred-row {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 4px 0;
            border-bottom: 1px solid rgba(30,58,138,.08);
        }

        .cred-row:last-child { border-bottom: none; }

        .cred-badge {
            font-size: 10px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 20px;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .badge-active { background: #dcfce7; color: #166534; }
        .badge-inactive { background: #e5e7eb; color: #374151; }
        .badge-officer { background: #dbeafe; color: #1d4ed8; }

        .cred-info { font-size: 11px; color: #1e40af; }

        /* Footer */
        .login-footer {
            background: #f8fafc;
            text-align: center;
            padding: 14px 20px;
            font-size: 11px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .login-footer img {
            height: 16px;
            width: auto;
            opacity: .4;
        }

        /* Outside card copyright */
        .login-copyright {
            text-align: center;
            margin-top: 16px;
            font-size: 11px;
            color: rgba(255,255,255,0.3);
        }
    </style>
</head>
<body>

<div class="login-wrap">

    {{-- KTM brand above card --}}
    <div class="ktm-brand">
        <img src="{{ asset('images/R.png') }}" alt="KTM Logo">
        <div class="ktm-brand-name">Keretapi Tanah Melayu Berhad</div>
    </div>

    <div class="login-card">

        {{-- Blue header --}}
        <div class="login-header">
            <div class="login-logo-wrap">
                <img src="{{ asset('images/R.png') }}" alt="KTM">
            </div>
            <div class="login-title">KTMe<span>DOIS</span></div>
            <div class="login-sub">Electronic Delivery Order &amp; Invoice System</div>
        </div>

        <div class="login-divider"></div>

        <div class="login-body">

            @if($errors->any())
            <div class="alert alert-error">
                <i class="fa fa-exclamation-circle" style="flex-shrink:0;margin-top:1px"></i>
                <span>{{ $errors->first() }}</span>
            </div>
            @endif

            @if(session('success'))
            <div class="alert alert-success">
                <i class="fa fa-check-circle" style="flex-shrink:0;margin-top:1px"></i>
                <span>{{ session('success') }}</span>
            </div>
            @endif

            {{-- Demo credentials --}}
            <div class="credentials-hint">
                <div class="hint-title"><i class="fa fa-key me-1"></i>Demo Credentials</div>
                <div class="cred-row">
                    <span class="cred-badge badge-active">Active Vendor</span>
                    <span class="cred-info">vendor@ktm.com &nbsp;/&nbsp; Vendor@123</span>
                </div>
                <div class="cred-row">
                    <span class="cred-badge badge-inactive">Inactive Vendor</span>
                    <span class="cred-info">railtech@ktm.com &nbsp;/&nbsp; Vendor@456</span>
                </div>
                <div class="cred-row">
                    <span class="cred-badge badge-officer">KTM Officer</span>
                    <span class="cred-info">officer@ktm.com &nbsp;/&nbsp; Officer@123</span>
                </div>
            </div>

            <form action="{{ route('login.post') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="email"><i class="fa fa-envelope me-1"></i>Email Address</label>
                    <div class="input-wrap">
                        <i class="fa fa-envelope"></i>
                        <input type="email" id="email" name="email"
                            value="{{ old('email') }}"
                            placeholder="Enter your KTMB email"
                            required autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password"><i class="fa fa-lock me-1"></i>Password</label>
                    <div class="input-wrap">
                        <i class="fa fa-lock"></i>
                        <input type="password" id="password" name="password"
                            placeholder="Enter your password"
                            required>
                    </div>
                    <label class="show-pw">
                        <input type="checkbox"
                            onchange="document.getElementById('password').type=this.checked?'text':'password'">
                        Show password
                    </label>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fa fa-sign-in-alt"></i>
                    Sign In to KTMeDOIS
                    <span class="btn-arrow"><i class="fa fa-chevron-right"></i></span>
                </button>

            </form>
        </div>

        <div class="login-footer">
            <img src="{{ asset('images/R.png') }}" alt="KTM">
            KTM Berhad &mdash; Authorized Personnel Only &mdash; KTMeDOIS 2026
        </div>

    </div>

    <div class="login-copyright">
        &copy; 2026 Keretapi Tanah Melayu Berhad. All rights reserved.
    </div>

</div>

</body>
</html>
