<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->comment('プラン名');
            $table->string('stripe_price_id')->comment('Stripe Price ID');
            $table->integer('monthly_price')->comment('月額料金（円）');
            $table->integer('max_users')->default(0)->comment('最大ユーザー数（0=無制限）');
            $table->json('features')->comment('利用可能機能');
            $table->boolean('is_active')->default(true)->comment('有効フラグ');
            $table->timestamps();

            $table->unique('stripe_price_id');
            $table->index(['is_active']);
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')->constrained('offices')->comment('事業所ID');
            $table->foreignId('plan_id')->constrained('plans')->comment('プランID');
            $table->string('stripe_subscription_id')->unique()->comment('Stripe Subscription ID');
            $table->string('stripe_customer_id')->comment('Stripe Customer ID');
            $table->enum('status', ['active', 'canceled', 'incomplete', 'past_due', 'trialing'])->comment('ステータス');
            $table->timestamp('current_period_start')->comment('現在の期間開始日');
            $table->timestamp('current_period_end')->comment('現在の期間終了日');
            $table->timestamp('trial_ends_at')->nullable()->comment('トライアル終了日');
            $table->timestamp('canceled_at')->nullable()->comment('キャンセル日');
            $table->timestamps();

            $table->index(['office_id', 'status']);
            $table->index(['status']);
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')->constrained('offices')->comment('事業所ID');
            $table->foreignId('subscription_id')->constrained('subscriptions')->comment('サブスクリプションID');
            $table->string('stripe_invoice_id')->unique()->comment('Stripe Invoice ID');
            $table->integer('amount')->comment('金額（円）');
            $table->enum('status', ['draft', 'open', 'paid', 'uncollectible', 'void'])->comment('ステータス');
            $table->timestamp('invoice_date')->comment('請求日');
            $table->timestamp('due_date')->nullable()->comment('支払期限');
            $table->timestamp('paid_at')->nullable()->comment('支払日');
            $table->json('line_items')->nullable()->comment('明細');
            $table->timestamps();

            $table->index(['office_id', 'status']);
            $table->index(['status', 'due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('plans');
    }
};