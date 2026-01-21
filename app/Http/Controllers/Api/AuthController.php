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
        ]);

        $roles = [
            'super_admin' => \App\Models\SuperAdmin::class,
            'admin' => \App\Models\Admin::class,
            'doctor' => \App\Models\Doctor::class,
            'attendant' => \App\Models\Attendant::class,
        ];

        foreach ($roles as $roleName => $modelClass) {
            $user = $modelClass::where('email', $validated['email'])->first();

            if ($user && Hash::check($validated['password'], $user->getAuthPassword())) {
                // Determine guard
                $token = auth($roleName)->login($user);

                if (! $token) {
                    continue;
                }

                // Update last login if column exists
                if (isset($user->last_login_at)) {
                    $user->last_login_at = now();
                    $user->save();
                }

                return $this->respondWithToken($token, $user, $roleName);
            }
        }

        return response()->json([
            'message' => 'Invalid credentials provided.'
        ], 401);
    }

    /**
     * Handle a logout request.
     */
    public function logout()
    {
        auth()->logout();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        try {
            $newToken = auth()->refresh();
            $user = auth()->user();
            
            // Determine role based on model type
            $role = 'unknown';
            if ($user instanceof \App\Models\SuperAdmin) $role = 'super_admin';
            elseif ($user instanceof \App\Models\Admin) $role = 'admin';
            elseif ($user instanceof \App\Models\Doctor) $role = 'doctor';
            elseif ($user instanceof \App\Models\Attendant) $role = 'attendant';

            return $this->respondWithToken($newToken, $user, $role);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Token refresh failed'], 401);
        }
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     * @param  mixed $user
     * @param  string $role
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token, $user, $role)
    {
        return response()->json([
            'access_token' => $token,
            'refresh_token' => $token, // JWT package uses the same token for refresh usually unless configured otherwise, but let's return it as requested
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => $user,
            'role' => $role
        ]);
    }

}
