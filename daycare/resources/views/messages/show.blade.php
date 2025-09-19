@extends('layouts.app')

@push('styles')
<style>
    .message-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px 10px 0 0;
    }
    .message-content {
        line-height: 1.8;
        font-size: 1rem;
        white-space: pre-wrap;
        word-wrap: break-word;
    }
    .message-meta {
        color: #6c757d;
        font-size: 0.9rem;
    }
    .pin-indicator {
        background-color: #ffc107;
        color: #212529;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: bold;
    }
    .important-indicator {
        background-color: #dc3545;
        color: white;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: bold;
    }
    .read-status {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 12px;
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>
        <i class="fas fa-comment-alt me-2"></i>メッセージ詳細
    </h3>
    <div class="d-flex gap-2">
        @if(auth()->user()->isAdmin())
            <button class="btn btn-warning" onclick="togglePin({{ $message->id }})">
                <i class="fas fa-thumbtack me-2"></i>
                {{ $message->is_pinned ? 'ピン留め解除' : 'ピン留め' }}
            </button>
        @endif
        <a href="{{ route('messages.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>一覧に戻る
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header message-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">{{ $message->title }}</h4>
                    <div class="d-flex gap-2">
                        @if($message->is_pinned)
                            <span class="pin-indicator">
                                <i class="fas fa-thumbtack me-1"></i>ピン留め
                            </span>
                        @endif
                        @if($message->is_important)
                            <span class="important-indicator">
                                <i class="fas fa-exclamation-triangle me-1"></i>重要
                            </span>
                        @endif
                    </div>
                </div>

                <div class="message-meta mt-3">
                    <div class="row">
                        <div class="col-md-6">
                            <i class="fas fa-user me-2"></i>{{ $message->user->name }}
                            <small class="ms-2">({{ $message->user->position }})</small>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <i class="fas fa-clock me-2"></i>{{ $message->created_at->format('Y年n月j日 H:i') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="message-content">
                    {{ $message->body }}
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- 既読状況 -->
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-eye me-2"></i>既読状況
                </h6>
            </div>
            <div class="card-body">
                <div class="read-status">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        <strong>{{ $message->read_count }}人が既読</strong>
                    </div>

                    @php
                        $officeUsers = auth()->user()->office->users()->active()->count();
                        $readPercentage = $officeUsers > 0 ? round(($message->read_count / $officeUsers) * 100) : 0;
                    @endphp

                    <div class="progress mb-2" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: {{ $readPercentage }}%"></div>
                    </div>

                    <small class="text-muted">
                        事業所職員{{ $officeUsers }}人中 {{ $message->read_count }}人が既読 ({{ $readPercentage }}%)
                    </small>
                </div>

                @if($message->messageReads->count() > 0)
                    <hr>
                    <h6 class="small fw-bold text-muted">既読者一覧</h6>
                    <div class="small">
                        @foreach($message->messageReads()->with('user')->latest('read_at')->get() as $read)
                            <div class="d-flex justify-content-between align-items-center py-1">
                                <span>{{ $read->user->name }}</span>
                                <small class="text-muted">{{ $read->read_at->format('m/d H:i') }}</small>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- メッセージ情報 -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>メッセージ情報
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <small class="text-muted d-block">投稿者</small>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user-circle fa-lg text-primary me-2"></i>
                            <div>
                                <div class="fw-bold">{{ $message->user->name }}</div>
                                <small class="text-muted">{{ $message->user->position }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <small class="text-muted d-block">投稿日時</small>
                        <div>
                            <i class="fas fa-calendar me-2"></i>{{ $message->created_at->format('Y年n月j日') }}<br>
                            <i class="fas fa-clock me-2"></i>{{ $message->created_at->format('H:i') }}
                        </div>
                    </div>

                    @if($message->created_at->ne($message->updated_at))
                        <div class="col-12">
                            <small class="text-muted d-block">更新日時</small>
                            <div>
                                <i class="fas fa-edit me-2"></i>{{ $message->updated_at->format('Y年n月j日 H:i') }}
                            </div>
                        </div>
                    @endif

                    <div class="col-12">
                        <small class="text-muted d-block">ステータス</small>
                        <div class="d-flex flex-wrap gap-1">
                            @if($message->is_important)
                                <span class="badge bg-danger">重要</span>
                            @endif
                            @if($message->is_pinned)
                                <span class="badge bg-warning text-dark">ピン留め</span>
                            @endif
                            @if(!$message->is_important && !$message->is_pinned)
                                <span class="badge bg-secondary">通常</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
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

// ページ表示時に既読にマーク
$(document).ready(function() {
    $.ajax({
        url: '{{ route("messages.read", $message) }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        }
    });
});
</script>
@endpush