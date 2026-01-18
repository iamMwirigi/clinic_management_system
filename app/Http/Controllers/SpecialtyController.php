<?php

namespace App\Http\Controllers;

use App\Models\Specialty;
use Illuminate\Http\Request;

class SpecialtyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Specialty::with(['hospital', 'doctor'])->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'hospital_id' => 'required|exists:hospitals,id',
            'doctor_id' => 'required|exists:users,id',
            'specialty_name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'consultation_fee' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $specialty = Specialty::create($validated);
        return response()->json($specialty, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Specialty $specialty)
    {
        return response()->json($specialty->load(['hospital', 'doctor']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Specialty $specialty)
    {
        $validated = $request->validate([
            'hospital_id' => 'exists:hospitals,id',
            'doctor_id' => 'exists:users,id',
            'specialty_name' => 'string|max:100',
            'description' => 'nullable|string',
            'consultation_fee' => 'numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $specialty->update($validated);
        return response()->json($specialty);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Specialty $specialty)
    {
        $specialty->delete();
        return response()->json(null, 204);
    }
}
