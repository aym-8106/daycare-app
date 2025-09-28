<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')->constrained('offices')->comment('事業所ID');
            $table->foreignId('user_id')->constrained('users')->comment('職員ID');
            $table->date('work_date')->comment('勤務日');
            $table->time('clock_in')->nullable()->comment('出勤時刻');
            $table->time('clock_out')->nullable()->comment('退勤時刻');
            $table->time('break_start')->nullable()->comment('休憩開始時刻');
            $table->time('break_end')->nullable()->comment('休憩終了時刻');
            $table->time('overtime_start')->nullable()->comment('残業開始時刻');
            $table->time('overtime_end')->nullable()->comment('残業終了時刻');
            $table->integer('break_minutes')->default(0)->comment('休憩時間（分）');
            $table->integer('overtime_minutes')->default(0)->comment('残業時間（分）');
            $table->integer('work_minutes')->default(0)->comment('実労働時間（分）');
            $table->enum('status', ['normal', 'late', 'early_leave', 'absent'])->default('normal')->comment('勤怠状況');
            $table->text('note')->nullable()->comment('備考');
            $table->boolean('is_locked')->default(false)->comment('締めロック');
            $table->timestamp('locked_at')->nullable()->comment('締め日時');
            $table->foreignId('locked_by')->nullable()->constrained('users')->comment('締め実行者');
            $table->timestamps();

            $table->unique(['office_id', 'user_id', 'work_date']);
            $table->index(['office_id', 'work_date']);
            $table->index(['office_id', 'user_id', 'work_date']);
            $table->index(['office_id', 'is_locked']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};