<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')->constrained('offices')->comment('事業所ID');
            $table->foreignId('user_id')->nullable()->constrained('users')->comment('実行者ID');
            $table->string('entity', 100)->comment('操作対象エンティティ');
            $table->unsignedBigInteger('entity_id')->nullable()->comment('操作対象ID');
            $table->enum('action', ['create', 'update', 'delete', 'login', 'logout'])->comment('操作種別');
            $table->json('payload')->nullable()->comment('操作内容詳細');
            $table->string('ip_address', 45)->nullable()->comment('IPアドレス');
            $table->string('user_agent')->nullable()->comment('ユーザーエージェント');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['office_id', 'created_at']);
            $table->index(['office_id', 'user_id', 'created_at']);
            $table->index(['office_id', 'entity', 'entity_id']);
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('audit_logs');
    }
};