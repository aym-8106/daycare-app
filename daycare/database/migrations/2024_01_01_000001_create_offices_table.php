<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offices', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->comment('事業所名');
            $table->text('address')->nullable()->comment('住所');
            $table->string('phone', 20)->nullable()->comment('電話番号');
            $table->string('email', 100)->nullable()->comment('メールアドレス');
            $table->json('business_days')->comment('営業日（1-7: 月-日）');
            $table->time('open_time')->default('08:30')->comment('開所時間');
            $table->time('close_time')->default('17:30')->comment('閉所時間');
            $table->integer('standard_work_hours')->default(8)->comment('標準労働時間（時間）');
            $table->integer('standard_break_minutes')->default(60)->comment('標準休憩時間（分）');
            $table->json('holidays')->nullable()->comment('定期休日設定');
            $table->boolean('is_active')->default(true)->comment('有効フラグ');
            $table->timestamps();

            $table->index(['is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offices');
    }
};