<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            // さくらデイサービス（office_id: 1）
            [
                'office_id' => 1,
                'employee_id' => 'ADM001',
                'name' => '山田 太郎',
                'email' => 'yamada@sakura.example.com',
                'password' => Hash::make('password123'),
                'employment_type' => 'full_time',
                'position' => '施設長',
                'available_days' => json_encode([1, 2, 3, 4, 5, 6]),
                'default_break_minutes' => 60,
                'paid_leave_days' => 20,
                'hire_date' => '2023-04-01',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'office_id' => 1,
                'employee_id' => 'STF001',
                'name' => '佐藤 花子',
                'email' => 'sato@sakura.example.com',
                'password' => Hash::make('password123'),
                'employment_type' => 'full_time',
                'position' => '介護職員',
                'available_days' => json_encode([1, 2, 3, 4, 5]),
                'default_break_minutes' => 60,
                'paid_leave_days' => 15,
                'hire_date' => '2023-06-01',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'office_id' => 1,
                'employee_id' => 'STF002',
                'name' => '田中 次郎',
                'email' => 'tanaka@sakura.example.com',
                'password' => Hash::make('password123'),
                'employment_type' => 'part_time',
                'position' => '機能訓練指導員',
                'available_days' => json_encode([1, 3, 5]),
                'default_break_minutes' => 30,
                'paid_leave_days' => 10,
                'hire_date' => '2023-08-01',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ひまわりデイサービス（office_id: 2）
            [
                'office_id' => 2,
                'employee_id' => 'ADM001',
                'name' => '鈴木 一郎',
                'email' => 'suzuki@himawari.example.com',
                'password' => Hash::make('password123'),
                'employment_type' => 'full_time',
                'position' => '施設長',
                'available_days' => json_encode([1, 2, 3, 4, 5]),
                'default_break_minutes' => 60,
                'paid_leave_days' => 20,
                'hire_date' => '2023-04-01',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'office_id' => 2,
                'employee_id' => 'STF001',
                'name' => '高橋 美和',
                'email' => 'takahashi@himawari.example.com',
                'password' => Hash::make('password123'),
                'employment_type' => 'full_time',
                'position' => '介護職員',
                'available_days' => json_encode([1, 2, 3, 4, 5]),
                'default_break_minutes' => 60,
                'paid_leave_days' => 18,
                'hire_date' => '2023-05-01',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('users')->insert($users);

        // ユーザーロール割当
        $userRoles = [
            ['user_id' => 1, 'role_id' => 1, 'created_at' => now(), 'updated_at' => now()], // 山田→管理者
            ['user_id' => 2, 'role_id' => 2, 'created_at' => now(), 'updated_at' => now()], // 佐藤→職員
            ['user_id' => 3, 'role_id' => 2, 'created_at' => now(), 'updated_at' => now()], // 田中→職員
            ['user_id' => 4, 'role_id' => 1, 'created_at' => now(), 'updated_at' => now()], // 鈴木→管理者
            ['user_id' => 5, 'role_id' => 2, 'created_at' => now(), 'updated_at' => now()], // 高橋→職員
        ];

        DB::table('user_roles')->insert($userRoles);
    }
}