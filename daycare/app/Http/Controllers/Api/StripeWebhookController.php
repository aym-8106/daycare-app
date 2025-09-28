<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        if (!$endpointSecret) {
            Log::error('Stripe webhook secret not configured');
            return response('Webhook secret not configured', 400);
        }

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $endpointSecret
            );
        } catch (\UnexpectedValueException $e) {
            Log::error('Invalid payload in Stripe webhook', ['error' => $e->getMessage()]);
            return response('Invalid payload', 400);
        } catch (SignatureVerificationException $e) {
            Log::error('Invalid signature in Stripe webhook', ['error' => $e->getMessage()]);
            return response('Invalid signature', 400);
        }

        // イベントタイプに応じて処理
        switch ($event->type) {
            case 'customer.subscription.created':
                $this->handleSubscriptionCreated($event->data->object);
                break;

            case 'customer.subscription.updated':
                $this->handleSubscriptionUpdated($event->data->object);
                break;

            case 'customer.subscription.deleted':
                $this->handleSubscriptionDeleted($event->data->object);
                break;

            case 'invoice.payment_succeeded':
                $this->handlePaymentSucceeded($event->data->object);
                break;

            case 'invoice.payment_failed':
                $this->handlePaymentFailed($event->data->object);
                break;

            default:
                Log::info('Unhandled Stripe webhook event', ['type' => $event->type]);
        }

        // 監査ログ記録
        AuditLog::create([
            'office_id' => null, // システムレベルのログ
            'user_id' => null,
            'entity' => 'stripe_webhook',
            'entity_id' => null,
            'action' => 'webhook_received',
            'payload' => [
                'event_type' => $event->type,
                'event_id' => $event->id,
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return response('Webhook handled', 200);
    }

    private function handleSubscriptionCreated($subscription)
    {
        Log::info('Subscription created', [
            'subscription_id' => $subscription->id,
            'customer_id' => $subscription->customer,
            'status' => $subscription->status,
        ]);

        // TODO: サブスクリプション作成時の処理
        // 例：
        // - office の有効化
        // - プラン情報の記録
        // - 通知メール送信
    }

    private function handleSubscriptionUpdated($subscription)
    {
        Log::info('Subscription updated', [
            'subscription_id' => $subscription->id,
            'customer_id' => $subscription->customer,
            'status' => $subscription->status,
        ]);

        // TODO: サブスクリプション更新時の処理
        // 例：
        // - プラン変更の反映
        // - 機能制限の更新
        // - 通知メール送信
    }

    private function handleSubscriptionDeleted($subscription)
    {
        Log::info('Subscription deleted', [
            'subscription_id' => $subscription->id,
            'customer_id' => $subscription->customer,
        ]);

        // TODO: サブスクリプション削除時の処理
        // 例：
        // - office の無効化
        // - データのアーカイブ
        // - 最終通知メール送信
    }

    private function handlePaymentSucceeded($invoice)
    {
        Log::info('Payment succeeded', [
            'invoice_id' => $invoice->id,
            'customer_id' => $invoice->customer,
            'amount' => $invoice->amount_paid,
        ]);

        // TODO: 支払い成功時の処理
        // 例：
        // - 請求書の記録
        // - サービス継続の確認
        // - 領収書メール送信
    }

    private function handlePaymentFailed($invoice)
    {
        Log::warning('Payment failed', [
            'invoice_id' => $invoice->id,
            'customer_id' => $invoice->customer,
            'amount' => $invoice->amount_due,
        ]);

        // TODO: 支払い失敗時の処理
        // 例：
        // - 事業所への通知
        // - 猶予期間の設定
        // - リトライスケジュール
    }
}