<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OfficeScope
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if (!$user->office_id) {
            abort(403, '事業所に所属していません');
        }

        // リクエストに事業所IDを設定
        $request->merge(['office_id' => $user->office_id]);

        return $next($request);
    }
}