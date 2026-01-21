<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DoctorController extends Controller
{
    /**
     * List all doctors.
     * Super Admin → all doctors
     * Admin → only doctors from their hospital
     */
    public function index()
    {
        $user = Auth::user();

        if ($user instanceof \App\Models\SuperAdmin) {
            return response()->json(Doctor::with('hospital')->get());
        }

        if ($user instanceof \App\Models\Admin) {
            return response()->json(
                Doctor::with('hospital')->where('hospital_id', $user->hospital_id)->get()
            );
        }

        return response()->json(['message' => 'Unauthorized'], 403);
    }


    /**
     * Create a doctor.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $data = $request->all();

        // Admin can only assign doctors to their hospital
        if ($user instanceof \App\Models\Admin) {
            $data['hospital_id'] = $user->hospital_id;
        }

        $validator = Validator::make($data, [
            'hospital_id' => 'required|exists:hospitals,id',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:doctors,email',
            'phone_number' => 'nullable|string|max:20',
            'gender' => ['nullable', Rule::in(['Male', 'Female', 'Other'])],
            'date_of_birth' => 'nullable|date',
            'license_number' => 'required|string|max:50|unique:doctors,license_number',
            'national_id' => 'nullable|string|max:20|unique:doctors,national_id',
            'specialization' => 'nullable|string|max:100',
            'qualifications' => 'nullable|string',
            'years_of_experience' => 'nullable|integer|min:0',
            'is_available' => 'boolean',
            'consultation_fee' => 'nullable|numeric|min:0',
            'work_start_time' => 'nullable|date_format:H:i:s',
            'work_end_time' => 'nullable|date_format:H:i:s|after:work_start_time',
            'emergency_contact_name' => 'nullable|string|max:150',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'status' => ['nullable', Rule::in(['active', 'inactive', 'suspended'])],
            'profile_photo' => 'nullable|string|max:255',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validatedData = $validator->validated();
        $validatedData['password'] = bcrypt($validatedData['password']);

        // Additional security for admins
        if ($user instanceof \App\Models\Admin &&
            $user->hospital_id != $validatedData['hospital_id']) {
            return response()->json([
                'message' => 'You can only add doctors to your own hospital.'
            ], 403);
        }

        $doctor = Doctor::create($validatedData);

        return response()->json($doctor, 201);
    }


    /**
     * Display a single doctor.
     */
    public function show(Doctor $doctor)
    {
        $user = Auth::user();

        if (
            $user instanceof \App\Models\SuperAdmin ||
            ($user instanceof \App\Models\Admin && $user->hospital_id == $doctor->hospital_id)
        ) {
            return response()->json($doctor->load('hospital'));
        }

        return response()->json(['message' => 'Forbidden'], 403);
    }


    /**
     * Update doctor details.
     */
    public function update(Request $request, Doctor $doctor)
    {
        $user = Auth::user();

        if (
            !($user instanceof \App\Models\SuperAdmin) &&
            !($user instanceof \App\Models\Admin && $user->hospital_id == $doctor->hospital_id)
        ) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validator = Validator::make($request->all(), [
            'hospital_id' => 'sometimes|required|exists:hospitals,id',
            'first_name' => 'sometimes|required|string|max:100',
            'last_name' => 'sometimes|required|string|max:100',
            'email' => 'sometimes|required|email|max:150|unique:doctors,email,' . $doctor->id,
            'phone_number' => 'nullable|string|max:20',
            'gender' => ['nullable', Rule::in(['Male', 'Female', 'Other'])],
            'date_of_birth' => 'nullable|date',
            'license_number' => 'sometimes|required|string|max:50|unique:doctors,license_number,' . $doctor->id,
            'national_id' => 'nullable|string|max:20|unique:doctors,national_id,' . $doctor->id,
            'specialization' => 'nullable|string|max:100',
            'qualifications' => 'nullable|string',
            'years_of_experience' => 'nullable|integer|min:0',
            'is_available' => 'boolean',
            'consultation_fee' => 'nullable|numeric|min:0',
            'work_start_time' => 'nullable|date_format:H:i:s',
            'work_end_time' => 'nullable|date_format:H:i:s|after:work_start_time',
            'emergency_contact_name' => 'nullable|string|max:150',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'status' => ['nullable', Rule::in(['active', 'inactive', 'suspended'])],
            'profile_photo' => 'nullable|string|max:255',
            'password' => 'sometimes|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validatedData = $validator->validated();
        if (isset($validatedData['password'])) {
            $validatedData['password'] = bcrypt($validatedData['password']);
        }

        // Admin cannot move doctor to another hospital
        if (
            $user instanceof \App\Models\Admin &&
            $request->has('hospital_id') &&
            $request->hospital_id != $user->hospital_id
        ) {
            return response()->json([
                'message' => 'You cannot transfer a doctor to another hospital.'
            ], 403);
        }

        $doctor->update($validator->validated());

        return response()->json([
            'message' => 'Doctor updated successfully',
            'data' => $doctor
        ]);
    }


    /**
     * Soft delete a doctor.
     */
    public function destroy(Doctor $doctor)
    {
        $user = Auth::user();

        if (
            !($user instanceof \App\Models\SuperAdmin) &&
            !($user instanceof \App\Models\Admin && $user->hospital_id == $doctor->hospital_id)
        ) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $doctor->delete();

        return response()->json(['message' => 'Doctor deleted successfully'], 200);
    }
}
