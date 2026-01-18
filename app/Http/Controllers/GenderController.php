<?php

namespace App\Http\Controllers;

use App\Models\Gender;
use Illuminate\Http\Request;

class GenderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Gender::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:genders',
            'name' => 'required|string|max:50|unique:genders',
            'is_active' => 'boolean',
        ]);

        $gender = Gender::create($validated);
        return response()->json($gender, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Gender $gender)
    {
        return response()->json($gender);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Gender $gender)
    {
        $validated = $request->validate([
            'code' => 'string|max:10|unique:genders,code,' . $gender->id,
            'name' => 'string|max:50|unique:genders,name,' . $gender->id,
            'is_active' => 'boolean',
        ]);

        $gender->update($validated);
        return response()->json($gender);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Gender $gender)
    {
        $gender->delete();
        return response()->json(null, 204);
    }
}
