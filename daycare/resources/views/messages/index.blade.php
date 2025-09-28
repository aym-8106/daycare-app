@extends('layouts.app')

@push('styles')
<style>
    .message-card {
        transition: all 0.2s ease;
        border-left: 4px solid transparent;
    }
    .message-card:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    .message-card.unread {
        border-left-color: #007bff;
        background-color: #f8f9ff;
    }
    .message-card.pinned {
        border-left-color: #ffc107;
        background-color: #fffbf0;
    }
    .message-card.important {
        border-left-color: #dc3545;
        background-color: #fff5f5;
    }
    .message-meta {
        font-size: 0.875rem;
        color: #6c757d;
    }
    .message-title {
        font-weight: 600;
        color: #495057;
        text-decoration: none;
    }
    .message-title:hover {
        color: #007bff;
        text-decoration: underline;
    }
    .message-body-preview {
        color: #6c757d;
        font-size: 0.9rem;
        line-height: 1.4;
    }
    .pin-btn {
        border: none;
        background: none;
        color: #ffc107;
        font-size: 1.2rem;
    }
    .pin-btn:hover {
        color: #e0a800;
    }
    .pin-btn.unpinned {
        color: #dee2e6;
    }
    .read-badge {
        font-size: 0.75rem;
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>
        <i class="fas fa-comments me-2"></i>掲示板
        @if($unreadCount > 0)
            <span class="badge bg-danger ms-2">{{ $unreadCount }}件未読</span>
        @endif
    </h3>
    <div class="d-flex gap-2">
        <a href="{{ route('messages.create') }}" class="btn btn-success">
            <i class="fas fa-plus me-2"></i>新規投稿
        </a>
    </div>
</div>

<!-- 検索フォーム -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('messages.index') }}" class="row align-items-center">
            <div class="col-md-10">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" class="form-control" name="search" value="{{ $search }}"
                           placeholder="タイトルや内容で検索...">
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">検索</button>
            </div>
        </form>
    </div>
</div>

@if($pinnedMessages->count() > 0)
<!-- ピン留めメッセージ -->
<div class="card mb-4">
    <div class="card-header bg-warning text-dark">
        <h6 class="mb-0">
            <i class="fas fa-thumbtack me-2"></i>ピン留めメッセージ
        </h6>
    </div>
    <div class="card-body">
        @foreach($pinnedMessages as $message)
            <div class="d-flex align-items-center {{ $loop->last ? '' : 'border-bottom' }} pb-2 mb-2">
                <div class="flex-grow-1">
                    <a href="{{ route('messages.show', $message) }}" class="fw-bold text-decoration-none">
                        {{ $message->title }}
                        @if($message->is_important)
                            <span class="badge bg-danger ms-1">重要</span>
                        @endif
                    </a>
                    <small class="text-muted d-block">
                        {{ $message->created_at->format('m/d H:i') }} - {{ $message->user->name }}
                    </small>
                </div>
                @if(!$message->isReadBy(auth()->user()))
                    <span class="badge bg-primary">未読</span>
                @endif
            </div>
        @endforeach
    </div>
</div>
@endif

<!-- メッセージ一覧 -->
<div class="row">
    @forelse($messages as $message)
        <div class="col-12 mb-3">
            <div class="card message-card
                {{ !$message->isReadBy(auth()->user()) ? 'unread' : '' }}
                {{ $message->is_pinned ? 'pinned' : '' }}
                {{ $message->is_important ? 'important' : '' }}">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center mb-2">
                                <a href="{{ route('messages.show', $message) }}" class="message-title">
                                    {{ $message->title }}
                                </a>
                                @if($message->is_pinned)
                                    <i class="fas fa-thumbtack text-warning ms-2" title="ピン留め"></i>
                                @endif
                                @if($message->is_important)
                                    <span class="badge bg-danger ms-2">重要</span>
                                @endif
                            </div>

                            <div class="message-body-preview mb-2">
                                {{ Str::limit(strip_tags($message->body), 150) }}
                            </div>

                            <div class="message-meta d-flex align-items-center">
                                <span class="me-3">
                                    <i class="fas fa-user me-1"></i>{{ $message->user->name }}
                                </span>
                                <span class="me-3">
                                    <i class="fas fa-clock me-1"></i>{{ $message->created_at->format('Y/m/d H:i') }}
                                </span>
                                <span class="me-3">
                                    <i class="fas fa-eye me-1"></i>{{ $message->read_count }}人が既読
                                </span>
                            </div>
                        </div>

                        <div class="d-flex flex-column align-items-end">
                            @if(!$message->isReadBy(auth()->user()))
                                <span class="badge bg-primary read-badge mb-2">未読</span>
                            @endif

                            @if(auth()->user()->isAdmin())
                                <button class="pin-btn {{ $message->is_pinned ? '' : 'unpinned' }}"
                                        onclick="togglePin({{ $message->id }})"
                                        title="{{ $message->is_pinned ? 'ピン留め解除' : 'ピン留め' }}">
                                    <i class="fas fa-thumbtack"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">メッセージがありません</h5>
                @if($search)
                    <p class="text-muted">「{{ $search }}」に一致するメッセージが見つかりませんでした。</p>
                    <a href="{{ route('messages.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-list me-2"></i>全てのメッセージを表示
                    </a>
                @else
                    <p class="text-muted">最初のメッセージを投稿してみましょう。</p>
                    <a href="{{ route('messages.create') }}" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>新規投稿
                    </a>
                @endif
            </div>
        </div>
    @endforelse
</div>

<!-- ページネーション -->
@if($messages->hasPages())
    <div class="d-flex justify-content-center">
        {{ $messages->appends(request()->query())->links() }}
    </div>
@endif
@endsection

@push('scripts')
<script>
function togglePin(messageId) {
    $.ajax({
        url: `/messages/${messageId}/pin`,
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                location.reload();
            }
        },
        error: function(xhr) {
            alert('操作に失敗しました: ' + xhr.responseJSON.error);
        }
    });
}

// 未読メッセージを自動で既読にする
$(document).ready(function() {
    $('.message-card.unread .message-title').click(function(e) {
        e.preventDefault();
        const href = $(this).attr('href');
        const messageId = href.split('/').pop();

        $.ajax({
            url: `/messages/${messageId}/read`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function() {
                window.location.href = href;
            },
            error: function() {
                window.location.href = href;
            }
        });
    });
});
</script>
@endpush