<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $tenant = $user->tenant;

        return view('tenant.settings.index', compact('user', 'tenant'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $tenant = $user->tenant;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:500',
            'gender' => 'nullable|string|in:Male,Female,Other',
            'date_of_birth' => 'nullable|date|before:today',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_number' => 'nullable|string|max:30',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:5120',
        ]);

        $userData = [
            'name' => $validated['name'],
            'contact_number' => $validated['contact_number'] ?? null,
            'address' => $validated['address'] ?? null,
        ];

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture && !str_starts_with($user->profile_picture, 'avatars/admin')) {
                if (!str_ends_with($user->profile_picture, '.svg')) {
                    FileUploadService::delete($user->profile_picture);
                }
            }
            $path = FileUploadService::store($request->file('profile_picture'), 'avatars');
            $userData['profile_picture'] = $path;
        }

        $user->update($userData);

        if ($tenant) {
            $tenantData = [
                'full_name' => $validated['name'],
                'contact_number' => $validated['contact_number'] ?? $tenant->contact_number,
                'address' => $validated['address'] ?? $tenant->address,
                'emergency_contact_name' => $validated['emergency_contact_name'] ?? $tenant->emergency_contact_name,
                'emergency_contact_number' => $validated['emergency_contact_number'] ?? $tenant->emergency_contact_number,
            ];

            if ($request->filled('gender')) {
                $tenantData['gender'] = $validated['gender'];
            }
            if ($request->filled('date_of_birth')) {
                $tenantData['date_of_birth'] = $validated['date_of_birth'];
            }

            if (!empty($validated['emergency_contact_name']) || !empty($validated['emergency_contact_number'])) {
                $contactName = $validated['emergency_contact_name'] ?? '';
                $contactPhone = !empty($validated['emergency_contact_number']) ? '(' . $validated['emergency_contact_number'] . ')' : '';
                $tenantData['emergency_contact'] = trim($contactName . ' ' . $contactPhone);
            }

            if (isset($userData['profile_picture'])) {
                $tenantData['profile_picture'] = $userData['profile_picture'];
            }

            $tenant->update($tenantData);
        }

        return redirect()->route('tenant.settings.index')
            ->with('success', 'Your profile information and photo have been updated.');
    }

    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('tenant.settings.index')
            ->with('success', 'Your password has been changed successfully.');
    }
}
