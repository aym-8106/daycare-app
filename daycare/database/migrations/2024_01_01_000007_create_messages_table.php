<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')->constrained('offices')->comment('事業所ID');
            $table->foreignId('user_id')->constrained('users')->comment('投稿者ID');
            $table->string('title', 200)->comment('タイトル');
            $table->text('body')->comment('本文');
            $table->boolean('is_pinned')->default(false)->comment('ピン留めフラグ');
            $table->boolean('is_important')->default(false)->comment('重要フラグ');
            $table->timestamps();

            $table->index(['office_id', 'created_at']);
            $table->index(['office_id', 'is_pinned', 'created_at']);
            $table->index(['office_id', 'is_important', 'created_at']);
        });

        Schema::create('message_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained('messages')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('read_at')->useCurrent();

            $table->unique(['message_id', 'user_id']);
            $table->index(['user_id', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message_reads');
        Schema::dropIfExists('messages');
    }
};