<?php

namespace App\Policies;

use App\Models\AttendanceRecord;
use App\Models\User;

class AttendanceRecordPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // 同一事業所内のユーザーなら閲覧可能
    }

    public function view(User $user, AttendanceRecord $attendanceRecord): bool
    {
        // 管理者は全て閲覧可能、職員は自分のもののみ
        return $user->isAdmin() || $user->id === $attendanceRecord->user_id;
    }

    public function create(User $user): bool
    {
        return true; // 全員打刻可能
    }

    public function update(User $user, AttendanceRecord $attendanceRecord): bool
    {
        // ロック済みは管理者のみ編集可能
        if ($attendanceRecord->is_locked) {
            return $user->isAdmin();
        }

        // 未ロックなら管理者または本人が編集可能
        return $user->isAdmin() || $user->id === $attendanceRecord->user_id;
    }

    public function delete(User $user, AttendanceRecord $attendanceRecord): bool
    {
        return $user->isAdmin() && !$attendanceRecord->is_locked;
    }

    public function lock(User $user, AttendanceRecord $attendanceRecord): bool
    {
        return $user->isAdmin();
    }

    public function unlock(User $user, AttendanceRecord $attendanceRecord): bool
    {
        return $user->isAdmin();
    }
}