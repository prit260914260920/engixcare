<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Admin Login — EngixCare</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@400,0&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #e8f5e9 0%, #f1f8e9 50%, #e0f2f1 100%);
        }
        .login-wrapper {
            width: 100%;
            max-width: 420px;
            padding: 1.5rem;
        }
        .login-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 8px 40px rgba(0, 0, 0, 0.10);
            padding: 2.5rem 2.25rem;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            justify-content: center;
            margin-bottom: 1.75rem;
        }
        .brand-icon {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, #2e7d32, #66bb6a);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.4rem;
        }
        .brand-name {
            font-size: 1.35rem;
            font-weight: 700;
            color: #1b5e20;
            letter-spacing: -0.4px;
        }
        .brand-name span {
            color: #66bb6a;
        }
        h2 {
            text-align: center;
            font-size: 1.1rem;
            font-weight: 600;
            color: #374151;
            margin: 0 0 0.4rem;
        }
        .subtitle {
            text-align: center;
            font-size: 0.82rem;
            color: #9ca3af;
            margin: 0 0 1.75rem;
        }
        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            margin-bottom: 1.25rem;
            font-size: 0.83rem;
            color: #dc2626;
        }
        .alert-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            margin-bottom: 1.25rem;
            font-size: 0.83rem;
            color: #16a34a;
        }
        .form-group {
            margin-bottom: 1.1rem;
        }
        .form-group label {
            display: block;
            font-size: 0.82rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.4rem;
        }
        .input-wrap {
            position: relative;
        }
        .input-wrap .material-symbols-outlined {
            position: absolute;
            left: 0.85rem;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.15rem;
            color: #9ca3af;
            pointer-events: none;
        }
        .input-wrap input {
            width: 100%;
            padding: 0.65rem 0.9rem 0.65rem 2.6rem;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            font-size: 0.9rem;
            font-family: 'Inter', sans-serif;
            color: #111827;
            background: #f9fafb;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }
        .input-wrap input:focus {
            border-color: #66bb6a;
            box-shadow: 0 0 0 3px rgba(102, 187, 106, 0.15);
            background: #fff;
        }
        .input-wrap input.is-invalid {
            border-color: #f87171;
        }
        .toggle-password {
            position: absolute;
            right: 0.85rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 1.15rem;
            color: #9ca3af;
            user-select: none;
        }
        .toggle-password:hover { color: #6b7280; }
        .remember-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }
        .remember-row input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #2e7d32;
            cursor: pointer;
        }
        .remember-row label {
            font-size: 0.83rem;
            color: #6b7280;
            cursor: pointer;
            margin: 0;
        }
        .btn-login {
            width: 100%;
            padding: 0.75rem;
            background: linear-gradient(135deg, #2e7d32, #43a047);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.15s;
            letter-spacing: 0.2px;
        }
        .btn-login:hover { opacity: 0.92; transform: translateY(-1px); }
        .btn-login:active { transform: translateY(0); opacity: 1; }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 1.25rem;
            font-size: 0.82rem;
            color: #9ca3af;
            text-decoration: none;
        }
        .back-link:hover { color: #6b7280; }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">

            {{-- Brand --}}
            <div class="brand">
                <div class="brand-icon">
                    <span class="material-symbols-outlined">admin_panel_settings</span>
                </div>
                <div class="brand-name">Engix<span>Care</span></div>
            </div>

            <h2>Admin Panel</h2>
            <p class="subtitle">Sign in to manage your store</p>

            {{-- Flash messages --}}
            @if (session('error'))
                <div class="alert-error">{{ session('error') }}</div>
            @endif

            {{-- Validation errors --}}
            @if ($errors->any())
                <div class="alert-error">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.submit') }}">
                @csrf

                {{-- Email --}}
                <div class="form-group">
                    <label for="email">Email address</label>
                    <div class="input-wrap">
                        <span class="material-symbols-outlined">mail</span>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="admin@example.com"
                            required
                            autofocus
                            class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                        />
                    </div>
                </div>

                {{-- Password --}}
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <span class="material-symbols-outlined">lock</span>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="••••••••"
                            required
                        />
                        <span class="material-symbols-outlined toggle-password" onclick="togglePassword()">visibility</span>
                    </div>
                </div>

                {{-- Remember me --}}
                <div class="remember-row">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Keep me signed in</label>
                </div>

                <button type="submit" class="btn-login">Sign in to Admin Panel</button>
            </form>

            <a href="{{ route('home') }}" class="back-link">← Back to main site</a>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon  = document.querySelector('.toggle-password');
            if (input.type === 'password') {
                input.type = 'text';
                icon.textContent = 'visibility_off';
            } else {
                input.type = 'password';
                icon.textContent = 'visibility';
            }
        }
    </script>
</body>
</html>
