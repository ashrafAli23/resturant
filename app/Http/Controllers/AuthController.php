<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\GeneralResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    use GeneralResponse;

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);


        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return $this->errorResponse(__('Invalid email or password'), Response::HTTP_UNAUTHORIZED);
        }
        $token = $request->user()->createToken("auth_token", ['admin-access'])->plainTextToken;
        return $this->dataResponse(["token" => $token], Response::HTTP_OK);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
    }
}