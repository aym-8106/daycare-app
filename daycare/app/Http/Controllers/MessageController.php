<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'office.scope']);
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $search = $request->get('search');

        $query = Message::byOffice($user->office_id)
            ->with(['user', 'messageReads'])
            ->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%");
            });
        }

        $messages = $query->paginate(20);

        // 未読メッセージ数を取得
        $unreadCount = Message::byOffice($user->office_id)
            ->whereDoesntHave('messageReads', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->count();

        // ピン留めメッセージを取得
        $pinnedMessages = Message::byOffice($user->office_id)
            ->pinned()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('messages.index', compact('messages', 'search', 'unreadCount', 'pinnedMessages'));
    }

    public function create()
    {
        return view('messages.create');
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'title' => 'required|string|max:200',
            'body' => 'required|string',
            'is_important' => 'boolean',
        ]);

        $message = Message::create([
            'office_id' => $user->office_id,
            'user_id' => $user->id,
            'title' => $request->title,
            'body' => $request->body,
            'is_important' => $request->boolean('is_important'),
        ]);

        AuditLog::log(
            $user->office_id,
            $user->id,
            'messages',
            $message->id,
            'create',
            $request->only(['title', 'is_important'])
        );

        return redirect()->route('messages.index')
            ->with('success', 'メッセージを投稿しました');
    }

    public function show(Message $message)
    {
        $user = auth()->user();

        // アクセス権限チェック
        if ($message->office_id !== $user->office_id) {
            abort(403, 'このメッセージにアクセスする権限がありません');
        }

        // 既読にする
        $message->markAsReadBy($user);

        return view('messages.show', compact('message'));
    }

    public function pin(Request $request, Message $message)
    {
        $user = auth()->user();

        if (!$user->isAdmin()) {
            return response()->json(['error' => 'ピン留め権限がありません'], 403);
        }

        if ($message->office_id !== $user->office_id) {
            return response()->json(['error' => 'アクセス権限がありません'], 403);
        }

        $message->update(['is_pinned' => !$message->is_pinned]);

        AuditLog::log(
            $user->office_id,
            $user->id,
            'messages',
            $message->id,
            'update',
            ['action' => 'pin', 'is_pinned' => $message->is_pinned]
        );

        return response()->json([
            'success' => true,
            'is_pinned' => $message->is_pinned,
            'message' => $message->is_pinned ? 'ピン留めしました' : 'ピン留めを解除しました'
        ]);
    }

    public function markAsRead(Request $request, Message $message)
    {
        $user = auth()->user();

        if ($message->office_id !== $user->office_id) {
            return response()->json(['error' => 'アクセス権限がありません'], 403);
        }

        $message->markAsReadBy($user);

        return response()->json(['success' => true, 'message' => '既読にしました']);
    }
}