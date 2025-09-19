<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')->constrained('offices')->comment('事業所ID');
            $table->date('work_date')->comment('業務日');
            $table->char('time_slot', 5)->comment('時間枠（HH:MM）');
            $table->foreignId('staff_id')->constrained('users')->comment('担当職員ID');
            $table->string('client_name', 100)->nullable()->comment('利用者名');
            $table->enum('activity', ['transport', 'bath', 'rehab', 'meal', 'recreation', 'other'])->comment('アクティビティ');
            $table->char('color', 7)->default('#FFFFFF')->comment('表示色（HEX）');
            $table->text('memo')->nullable()->comment('メモ');
            $table->timestamps();

            $table->index(['office_id', 'work_date']);
            $table->index(['office_id', 'work_date', 'time_slot']);
            $table->index(['office_id', 'staff_id', 'work_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_schedules');
    }
};