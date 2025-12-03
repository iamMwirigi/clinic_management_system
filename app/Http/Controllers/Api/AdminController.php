<?php

namespace App\Http\Controllers\Api;

use App\Models\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Admin::all());
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return response()->json(Admin::findOrFail($id));
    }

    /**
     * Store a newly created admin.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hospital_id' => 'required|exists:hospitals,id',
            'name' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:admins',
            'phone' => 'nullable|string|max:15',
            'password' => 'required|string|min:8',
            'role' => 'string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $admin = Admin::create([
            'hospital_id' => $request->hospital_id,
            'name'        => $request->name,
            'email'       => $request->email,
            'phone'       => $request->phone,
            'password'    => Hash::make($request->password),
            'role'        => $request->role,
        ]);

        return response()->json($admin, 201);
    }

    /**
     * Update the specified admin.
     */
    public function update(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'     => 'string|max:100',
            'email'    => 'string|email|max:100|unique:admins,email,' . $id,
            'phone'    => 'nullable|string|max:15',
            'role'     => 'string|max:50',
            'password' => 'nullable|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->all();

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $admin->update($data);

        return response()->json([
            'message' => 'Admin updated successfully',
            'data'    => $admin
        ]);
    }

    /**
     * Remove an admin.
     */
    public function destroy($id)
    {
        $admin = Admin::findOrFail($id);
        $admin->delete();

        return response()->json([
            'message' => 'Admin deleted successfully'
        ], 200);
    }
}