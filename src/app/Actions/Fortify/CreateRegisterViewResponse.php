<?php

namespace App\Actions\Fortify;

use Laravel\Fortify\Contracts\RegisterViewResponse;

class CreateRegisterViewResponse implements RegisterViewResponse
{
    public function toResponse($request)
    {
        return view('auth.register');
    }
}