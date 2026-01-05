<?php

namespace App\Actions\Fortify;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        if ($request->user() && $request->user()->is_admin) {
            return redirect()->intended('/admin/attendance/list');
        }

        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended('/attendance');
        }

        return redirect()->route('verification.notice');
    }
}