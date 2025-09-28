<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'スタータープラン',
                'stripe_price_id' => 'price_starter_monthly', // 実際のStripe Price IDに置き換え
                'monthly_price' => 9800,
                'max_users' => 10,
                'features' => json_encode([
                    'attendance_management',
                    'shift_management',
                    'basic_schedule',
                    'basic_messages',
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'スタンダードプラン',
                'stripe_price_id' => 'price_standard_monthly', // 実際のStripe Price IDに置き換え
                'monthly_price' => 19800,
                'max_users' => 30,
                'features' => json_encode([
                    'attendance_management',
                    'shift_management',
                    'advanced_schedule',
                    'advanced_messages',
                    'csv_export',
                    'audit_logs',
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'プレミアムプラン',
                'stripe_price_id' => 'price_premium_monthly', // 実際のStripe Price IDに置き換え
                'monthly_price' => 39800,
                'max_users' => 0, // 無制限
                'features' => json_encode([
                    'attendance_management',
                    'shift_management',
                    'advanced_schedule',
                    'advanced_messages',
                    'csv_export',
                    'audit_logs',
                    'api_access',
                    'custom_reports',
                    'priority_support',
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('plans')->insert($plans);
    }
}