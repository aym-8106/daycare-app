@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>
        <i class="fas fa-edit me-2"></i>新規メッセージ投稿
    </h3>
    <a href="{{ route('messages.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>戻る
    </a>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">メッセージ投稿フォーム</h5>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('messages.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="title" class="form-label">タイトル <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                               id="title" name="title" value="{{ old('title') }}" required maxlength="200"
                               placeholder="メッセージのタイトルを入力してください">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">最大200文字まで入力できます。</div>
                    </div>

                    <div class="mb-3">
                        <label for="body" class="form-label">内容 <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('body') is-invalid @enderror"
                                  id="body" name="body" rows="8" required
                                  placeholder="メッセージの内容を入力してください">{{ old('body') }}</textarea>
                        @error('body')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_important" name="is_important"
                                   value="1" {{ old('is_important') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_important">
                                <i class="fas fa-exclamation-triangle text-danger me-1"></i>
                                重要なお知らせとしてマークする
                            </label>
                        </div>
                        <div class="form-text">重要なお知らせは一覧で目立つ表示になります。</div>
                    </div>

                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="fas fa-info-circle me-2"></i>投稿について
                            </h6>
                            <ul class="mb-0 small text-muted">
                                <li>投稿したメッセージは同じ事業所の全職員が閲覧できます</li>
                                <li>管理者はメッセージをピン留めして重要な情報を上部に固定表示できます</li>
                                <li>投稿後の編集・削除は管理者に依頼してください</li>
                                <li>業務に関連する内容のみ投稿してください</li>
                            </ul>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('messages.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>キャンセル
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-paper-plane me-2"></i>投稿する
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// 文字数カウンター
$('#title').on('input', function() {
    const current = $(this).val().length;
    const max = 200;
    const remaining = max - current;

    let className = 'text-muted';
    if (remaining < 50) className = 'text-warning';
    if (remaining < 20) className = 'text-danger';

    $(this).next('.invalid-feedback').length ||
    $(this).next('.form-text').html(`最大200文字まで入力できます。<span class="${className}">（残り${remaining}文字）</span>`);
});

// 自動保存（ローカルストレージ）
function saveToLocalStorage() {
    const title = $('#title').val();
    const body = $('#body').val();
    const isImportant = $('#is_important').is(':checked');

    localStorage.setItem('message_draft', JSON.stringify({
        title: title,
        body: body,
        is_important: isImportant,
        timestamp: Date.now()
    }));
}

function loadFromLocalStorage() {
    const draft = localStorage.getItem('message_draft');
    if (draft) {
        const data = JSON.parse(draft);
        // 24時間以内のドラフトのみ復元
        if (Date.now() - data.timestamp < 24 * 60 * 60 * 1000) {
            if (data.title) $('#title').val(data.title);
            if (data.body) $('#body').val(data.body);
            if (data.is_important) $('#is_important').prop('checked', true);

            // 復元完了メッセージ
            if (data.title || data.body) {
                $('<div class="alert alert-info alert-dismissible fade show" role="alert">' +
                  '<i class="fas fa-info-circle me-2"></i>前回の下書きを復元しました。' +
                  '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                  '</div>').insertBefore('.card');
            }
        }
    }
}

// 自動保存イベント
$('#title, #body').on('input', function() {
    saveToLocalStorage();
});

$('#is_important').on('change', function() {
    saveToLocalStorage();
});

// フォーム送信時にドラフトをクリア
$('form').on('submit', function() {
    localStorage.removeItem('message_draft');
});

// ページ読み込み時にドラフトを復元
$(document).ready(function() {
    loadFromLocalStorage();
});

// ページ離脱時の確認
$(window).on('beforeunload', function() {
    const title = $('#title').val().trim();
    const body = $('#body').val().trim();

    if (title || body) {
        return '入力中の内容が失われます。このページを離れてもよろしいですか？';
    }
});
</script>
@endpush