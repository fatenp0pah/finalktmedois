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
            background: #fafaf8;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-wrap { width: 100%; max-width: 460px; padding: 20px; }
        .login-card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.10);
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, #d97706 0%, #92400e 60%, #3b1a08 100%);
            padding: 28px 30px 22px;
            text-align: center;
        }
        .login-logo {
            width: 56px; height: 56px;
            background: #fff;
            border-radius: 12px;
            display: inline-flex; align-items: center; justify-content: center;
            margin-bottom: 12px;
        }
        .login-logo i { font-size: 28px; color: #dc2626; }
        .login-title { color: #fff; font-size: 22px; font-weight: 800; margin-bottom: 4px; }
        .login-sub { color: rgba(255,255,255,0.8); font-size: 12px; }
        .login-body { padding: 28px 30px; }
        .form-group { margin-bottom: 18px; }
        label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 7px; }
        input[type=email], input[type=password] {
            width: 100%; padding: 11px 14px;
            border: 1.5px solid #d1d5db; border-radius: 10px;
            font-size: 14px; outline: none; background: #fafaf8;
            transition: border-color .2s;
        }
        input:focus { border-color: #d97706; box-shadow: 0 0 0 3px rgba(217,119,6,.12); }
        .btn-login {
            width: 100%; padding: 12px;
            background: linear-gradient(135deg, #d97706, #92400e);
            color: #fff; border: none; border-radius: 10px;
            font-size: 15px; font-weight: 700; cursor: pointer; margin-top: 8px;
            transition: opacity .2s;
        }
        .btn-login:hover { opacity: .9; }
        .alert { padding: 12px 14px; border-radius: 10px; font-size: 13px; margin-bottom: 16px; }
        .alert-error { background: #fee2e2; color: #991b1b; border-left: 4px solid #dc2626; }
        .alert-success { background: #dcfce7; color: #166534; border-left: 4px solid #22c55e; }
        .show-pw { display: flex; align-items: center; gap: 8px; font-size: 12px; color: #6b7280; margin-top: -10px; margin-bottom: 16px; cursor: pointer; }
        .show-pw input { width: auto; }
        .login-footer { text-align: center; padding: 16px; font-size: 11px; color: #9ca3af; border-top: 1px solid #f3f4f6; }
        .credentials-hint {
            background: #fffbeb; border: 1px solid #fde68a;
            border-radius: 10px; padding: 12px 14px; margin-bottom: 16px; font-size: 12px; color: #78350f;
        }
        .credentials-hint strong { display: block; margin-bottom: 6px; }
    </style>
</head>
<body>
<div class="login-wrap">
    <div class="login-card">
        <div class="login-header">
            <div class="login-logo"><i class="fa fa-train"></i></div>
            <div class="login-title">KTMeDOIS</div>
            <div class="login-sub">Electronic Delivery Order & Invoice System</div>
        </div>
        <div class="login-body">

            @if($errors->any())
            <div class="alert alert-error">
                <i class="fa fa-exclamation-circle me-1"></i>
                {{ $errors->first() }}
            </div>
            @endif

            @if(session('success'))
            <div class="alert alert-success">
                <i class="fa fa-check-circle me-1"></i>
                {{ session('success') }}
            </div>
            @endif

            {{-- Demo credentials hint for presentation --}}
            <div class="credentials-hint">
                <strong><i class="fa fa-info-circle me-1"></i>Demo Credentials</strong>
                <b>Vendor (Active):</b> vendor@ktm.com / Vendor@123<br>
                <b>Vendor (Inactive):</b> railtech@ktm.com / Vendor@456<br>
                <b>Officer:</b> officer@ktm.com / Officer@123
            </div>

            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="email"><i class="fa fa-envelope me-1"></i>Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                        placeholder="Enter your email" required autofocus>
                </div>
                <div class="form-group">
                    <label for="password"><i class="fa fa-lock me-1"></i>Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>
                <label class="show-pw">
                    <input type="checkbox" onchange="document.getElementById('password').type=this.checked?'text':'password'">
                    Show Password
                </label>
                <button type="submit" class="btn-login">
                    <i class="fa fa-sign-in-alt me-1"></i> Login
                </button>
            </form>
        </div>
        <div class="login-footer">
            KTM Berhad Internal Prototype System — Authorized Access Only
        </div>
    </div>
</div>
</body>
</html>
