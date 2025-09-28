<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Office;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        $offices = Office::active()->get();
        return view('auth.register', compact('offices'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'office_id' => ['required', 'exists:offices,id'],
            'employee_id' => ['required', 'string', 'max:20'],
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'position' => ['required', 'string', 'max:50'],
            'employment_type' => ['required', 'in:full_time,part_time'],
            'hire_date' => ['required', 'date'],
        ]);

        // 同一事業所内で職員IDの重複チェック
        if (User::where('office_id', $request->office_id)
                 ->where('employee_id', $request->employee_id)
                 ->exists()) {
            return back()->withErrors(['employee_id' => 'この職員IDは既に使用されています。']);
        }

        $user = User::create([
            'office_id' => $request->office_id,
            'employee_id' => $request->employee_id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'position' => $request->position,
            'employment_type' => $request->employment_type,
            'available_days' => [1, 2, 3, 4, 5], // デフォルト：月-金
            'hire_date' => $request->hire_date,
        ]);

        // デフォルトでstaffロールを付与
        $user->roles()->attach(2); // staff role id

        return redirect()->route('login')->with('success', 'ユーザー登録が完了しました。ログインしてください。');
    }
}