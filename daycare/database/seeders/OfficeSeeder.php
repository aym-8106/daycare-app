<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OfficeSeeder extends Seeder
{
    public function run(): void
    {
        $offices = [
            [
                'name' => 'さくらデイサービス',
                'address' => '東京都世田谷区三軒茶屋1-1-1',
                'phone' => '03-1234-5678',
                'email' => 'sakura@example.com',
                'business_days' => json_encode([1, 2, 3, 4, 5, 6]), // 月-土
                'open_time' => '08:30:00',
                'close_time' => '17:30:00',
                'standard_work_hours' => 8,
                'standard_break_minutes' => 60,
                'holidays' => json_encode(['2024-12-29', '2024-12-30', '2024-12-31', '2025-01-01', '2025-01-02', '2025-01-03']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ひまわりデイサービス',
                'address' => '東京都渋谷区恵比寿2-2-2',
                'phone' => '03-2345-6789',
                'email' => 'himawari@example.com',
                'business_days' => json_encode([1, 2, 3, 4, 5]), // 月-金
                'open_time' => '09:00:00',
                'close_time' => '18:00:00',
                'standard_work_hours' => 8,
                'standard_break_minutes' => 60,
                'holidays' => json_encode(['2024-12-29', '2024-12-30', '2024-12-31', '2025-01-01', '2025-01-02', '2025-01-03']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('offices')->insert($offices);
    }
}