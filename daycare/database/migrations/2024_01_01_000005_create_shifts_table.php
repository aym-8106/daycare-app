<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')->constrained('offices')->comment('事業所ID');
            $table->foreignId('user_id')->constrained('users')->comment('職員ID');
            $table->char('year_month', 6)->comment('年月(YYYYMM)');
            $table->tinyInteger('day')->comment('日（1-31）');
            $table->string('shift_code', 20)->comment('シフト区分（早/日/遅/休など）');
            $table->time('start_time')->nullable()->comment('開始時刻');
            $table->time('end_time')->nullable()->comment('終了時刻');
            $table->text('note')->nullable()->comment('備考');
            $table->boolean('is_confirmed')->default(false)->comment('確定フラグ');
            $table->timestamps();

            $table->unique(['office_id', 'user_id', 'year_month', 'day']);
            $table->index(['office_id', 'year_month']);
            $table->index(['office_id', 'year_month', 'day']);
            $table->index(['office_id', 'user_id', 'year_month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};