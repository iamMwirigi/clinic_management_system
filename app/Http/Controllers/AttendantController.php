<?php

namespace App\Http\Controllers;

use App\Models\Attendant;
use Illuminate\Http\Request;

class AttendantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user instanceof \App\Models\SuperAdmin) {
            return response()->json(Attendant::with('hospital')->get());
        }

        if ($user instanceof \App\Models\Admin) {
            return response()->json(
                Attendant::with('hospital')->where('hospital_id', $user->hospital_id)->get()
            );
        }

        return response()->json(['message' => 'Unauthorized'], 403);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $data = $request->all();

        if ($user instanceof \App\Models\Admin) {
            $data['hospital_id'] = $user->hospital_id;
        }

        $validated = $request->validate([
            'hospital_id' => 'required|exists:hospitals,id',
            'email' => 'required|email|max:100|unique:attendants,email',
            'phone' => 'nullable|string|max:15',
            'password' => 'required|string|min:8',
            'is_active' => 'boolean',
        ]);

        // Security check for admin
        if ($user instanceof \App\Models\Admin && $user->hospital_id != $validated['hospital_id']) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated['password_hash'] = bcrypt($validated['password']);
        unset($validated['password']);

        $attendant = Attendant::create($validated);
        return response()->json($attendant, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Attendant $attendant)
    {
        $user = auth()->user();

        if (
            $user instanceof \App\Models\SuperAdmin ||
            ($user instanceof \App\Models\Admin && $user->hospital_id == $attendant->hospital_id)
        ) {
            return response()->json($attendant->load('hospital'));
        }

        return response()->json(['message' => 'Forbidden'], 403);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attendant $attendant)
    {
        $user = auth()->user();

        if (
            !($user instanceof \App\Models\SuperAdmin) &&
            !($user instanceof \App\Models\Admin && $user->hospital_id == $attendant->hospital_id)
        ) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'hospital_id' => 'exists:hospitals,id',
            'email' => 'email|max:100|unique:attendants,email,' . $attendant->id,
            'phone' => 'nullable|string|max:15',
            'password' => 'sometimes|string|min:8',
            'is_active' => 'boolean',
        ]);

        if (isset($validated['password'])) {
            $validated['password_hash'] = bcrypt($validated['password']);
            unset($validated['password']);
        }

        $attendant->update($validated);
        return response()->json($attendant);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendant $attendant)
    {
        $user = auth()->user();

        if (
            !($user instanceof \App\Models\SuperAdmin) &&
            !($user instanceof \App\Models\Admin && $user->hospital_id == $attendant->hospital_id)
        ) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $attendant->delete();
        return response()->json(null, 204);
    }
}
