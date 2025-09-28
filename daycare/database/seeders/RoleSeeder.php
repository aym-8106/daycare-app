<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin_office',
                'display_name' => '事業所管理者',
                'description' => '事業所の全データ閲覧・編集権限',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'staff',
                'display_name' => '一般職員',
                'description' => '自分の勤怠・シフト閲覧、メッセージ参加権限',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('roles')->insert($roles);
    }
}