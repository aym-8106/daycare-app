<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')->constrained('offices')->comment('事業所ID');
            $table->string('employee_id', 20)->comment('職員ID');
            $table->string('name', 100)->comment('氏名');
            $table->string('email')->unique()->comment('メールアドレス');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->comment('パスワード');
            $table->enum('employment_type', ['full_time', 'part_time'])->default('full_time')->comment('雇用形態');
            $table->string('position', 50)->nullable()->comment('職種');
            $table->json('available_days')->comment('勤務可能曜日（1-7: 月-日）');
            $table->integer('default_break_minutes')->default(60)->comment('デフォルト休憩時間（分）');
            $table->integer('paid_leave_days')->default(20)->comment('有給残日数');
            $table->date('hire_date')->comment('入社日');
            $table->boolean('is_active')->default(true)->comment('有効フラグ');
            $table->rememberToken();
            $table->timestamps();

            $table->unique(['office_id', 'employee_id']);
            $table->index(['office_id', 'is_active']);
            $table->index(['office_id', 'employment_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};