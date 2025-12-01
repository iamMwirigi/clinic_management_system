<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SuperAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SuperAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // In a real application, you'd want to add authentication and authorization here
        // to ensure only authorized users can list super admins.
        $superAdmins = SuperAdmin::all();
        return response()->json($superAdmins);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // You should also have authorization here.

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:super_admins',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:15',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $superAdmin = SuperAdmin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
        ]);

        return response()->json([
            'message' => 'Super Admin created successfully',
            'data' => $superAdmin
        ], 201);
    }

    // Note: The --api flag also creates stubs for show, update, and destroy.
    // You can implement them as needed. For example:
    // public function show(SuperAdmin $superAdmin) { ... }
    // public function update(Request $request, SuperAdmin $superAdmin) { ... }
    // public function destroy(SuperAdmin $superAdmin) { ... }
}