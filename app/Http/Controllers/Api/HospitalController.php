<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class HospitalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        // Super admin can see all hospitals
        if ($user instanceof \App\Models\SuperAdmin) {
            return response()->json(Hospital::all());
        }

        // Regular admin can only see their own hospital
        if ($user instanceof \App\Models\Admin) {
            return response()->json(Hospital::where('id', $user->hospital_id)->get());
        }

        return response()->json(['message' => 'Unauthorized'], 403);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Only Super Admins can create hospitals
        if (! (Auth::user() instanceof \App\Models\SuperAdmin)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'county' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:15',
            'email' => 'required|string|email|max:100|unique:hospitals',
            'logo_url' => 'nullable|url|max:255',
            'subscription_plan' => 'string|max:50',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $hospital = Hospital::create($request->all());

        return response()->json($hospital, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Hospital $hospital)
    {
        $user = Auth::user();

        // Super admin can see any hospital
        if ($user instanceof \App\Models\SuperAdmin) {
            return response()->json($hospital);
        }

        // Admin can only see their own hospital
        if ($user instanceof \App\Models\Admin && $user->hospital_id == $hospital->id) {
            return response()->json($hospital);
        }

        return response()->json(['message' => 'Forbidden'], 403);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Hospital $hospital)
    {
        $user = Auth::user();

        // Super admin can update any hospital
        // Admin can only update their own hospital
        if (!($user instanceof \App\Models\SuperAdmin || ($user instanceof \App\Models\Admin && $user->hospital_id == $hospital->id))) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'address' => 'string',
            'county' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:15',
            'email' => 'string|email|max:100|unique:hospitals,email,' . $hospital->id,
            'logo_url' => 'nullable|url|max:255',
            'subscription_plan' => 'string|max:50',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $hospital->update($request->all());

        return response()->json([
            'message' => 'Hospital updated successfully',
            'data' => $hospital
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Hospital $hospital)
    {
        // Only Super Admins can delete hospitals
        if (! (Auth::user() instanceof \App\Models\SuperAdmin)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $hospital->delete();

        return response()->json([
            'message' => 'Hospital deleted successfully'
        ], 200);
    }
}