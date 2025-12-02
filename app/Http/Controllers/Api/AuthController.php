<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Handle a login request for any role.
     */
   public function login(Request $request)
{
    $validated = $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
        'role' => ['required', 'string', Rule::in(['super_admin', 'admin', 'user'])],
    ]);

    $guard = $validated['role'];

    // Map role to model
    $model = null;
    if ($guard === 'super_admin') {
        $model = \App\Models\SuperAdmin::class;
    } else if ($guard === 'admin') {
        $model = \App\Models\Admin::class;
    }

    // Find user
    $user = $model::where('email', $validated['email'])->first();

    // Validate password
    if (! $user || !Hash::check($validated['password'], $user->password)) {
        return response()->json([
            'message' => 'Invalid credentials provided.'
        ], 401);
    }

    // Update last login time
    $user->last_login_at = now();
    $user->save();

    // Create token
    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
        'message' => 'Login successful',
        'user' => $user,
        'token' => $token,
        'role' => $guard,
    ]);
}


    /**
     * Handle a logout request.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

}
