<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', '通所介護管理システム') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .navbar-brand {
            font-weight: bold;
        }
        .alert {
            margin-bottom: 0;
        }
        .btn-clock {
            min-width: 120px;
            font-size: 1.1rem;
            font-weight: bold;
        }
        .btn-clock-in {
            background-color: #28a745;
            border-color: #28a745;
        }
        .btn-clock-out {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .btn-break {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #212529;
        }
        .attendance-card {
            border-left: 4px solid #007bff;
        }
        .time-display {
            font-family: 'Courier New', monospace;
            font-size: 1.2rem;
            font-weight: bold;
        }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="fas fa-home me-2"></i>{{ config('app.name') }}
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('attendance.index') }}">
                            <i class="fas fa-clock me-1"></i>勤怠打刻
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('shifts.index') }}">
                            <i class="fas fa-calendar-alt me-1"></i>シフト管理
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('schedule.index') }}">
                            <i class="fas fa-calendar-day me-1"></i>日次スケジュール
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('messages.index') }}">
                            <i class="fas fa-comments me-1"></i>掲示板
                        </a>
                    </li>
                </ul>

                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i>{{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('attendance.monthly') }}">勤怠確認</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt me-1"></i>ログアウト
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <main class="container my-4">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script>
        // 現在時刻を表示
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('ja-JP', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            document.querySelectorAll('.current-time').forEach(el => {
                el.textContent = timeString;
            });
        }

        setInterval(updateClock, 1000);
        updateClock();

        // CSRF トークン設定
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    @stack('scripts')
</body>
</html>