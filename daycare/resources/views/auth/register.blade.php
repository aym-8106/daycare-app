<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>新規登録 - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .register-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        .register-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card register-card">
                    <div class="card-header register-header text-center py-4">
                        <h3 class="mb-0">
                            <i class="fas fa-user-plus me-2"></i>新規職員登録
                        </h3>
                        <p class="mb-0 mt-2">{{ config('app.name') }}</p>
                    </div>

                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="office_id" class="form-label">事業所</label>
                                    <select class="form-select @error('office_id') is-invalid @enderror" name="office_id" required>
                                        <option value="">事業所を選択してください</option>
                                        @foreach($offices as $office)
                                            <option value="{{ $office->id }}" {{ old('office_id') == $office->id ? 'selected' : '' }}>
                                                {{ $office->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('office_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="employee_id" class="form-label">職員ID</label>
                                    <input type="text" class="form-control @error('employee_id') is-invalid @enderror"
                                           name="employee_id" value="{{ old('employee_id') }}" required maxlength="20">
                                    @error('employee_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="name" class="form-label">氏名</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       name="name" value="{{ old('name') }}" required maxlength="100">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">メールアドレス</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">パスワード</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                           name="password" required minlength="8">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="password_confirmation" class="form-label">パスワード確認</label>
                                    <input type="password" class="form-control" name="password_confirmation" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="position" class="form-label">職種</label>
                                    <input type="text" class="form-control @error('position') is-invalid @enderror"
                                           name="position" value="{{ old('position') }}" required maxlength="50"
                                           placeholder="例：介護職員、機能訓練指導員">
                                    @error('position')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="employment_type" class="form-label">雇用形態</label>
                                    <select class="form-select @error('employment_type') is-invalid @enderror" name="employment_type" required>
                                        <option value="">選択してください</option>
                                        <option value="full_time" {{ old('employment_type') == 'full_time' ? 'selected' : '' }}>常勤</option>
                                        <option value="part_time" {{ old('employment_type') == 'part_time' ? 'selected' : '' }}>非常勤</option>
                                    </select>
                                    @error('employment_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="hire_date" class="form-label">入社日</label>
                                <input type="date" class="form-control @error('hire_date') is-invalid @enderror"
                                       name="hire_date" value="{{ old('hire_date') }}" required>
                                @error('hire_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-user-plus me-2"></i>登録
                                </button>
                            </div>
                        </form>

                        <div class="text-center">
                            <p class="text-muted">既にアカウントをお持ちの場合</p>
                            <a href="{{ route('login') }}" class="btn btn-outline-primary">
                                <i class="fas fa-sign-in-alt me-2"></i>ログイン
                            </a>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <small class="text-white">
                        © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>