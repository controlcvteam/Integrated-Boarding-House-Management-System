<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
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
        $gcashName = Setting::get('gcash_name', 'Admin');
        $gcashNumber = Setting::get('gcash_number', '0917-888-9999');
        $gcashQrPath = Setting::get('gcash_qr_path', 'settings/default-gcash-qr.svg');

        return view('admin.settings.index', compact('user', 'gcashName', 'gcashNumber', 'gcashQrPath'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'contact_number' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:500',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:5120',
        ]);

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture && !str_starts_with($user->profile_picture, 'avatars/admin')) {
                FileUploadService::delete($user->profile_picture);
            }
            $validated['profile_picture'] = FileUploadService::store($request->file('profile_picture'), 'avatars');
        }

        $user->update($validated);

        return redirect()->route('admin.settings.index')
            ->with('success', 'Admin profile updated successfully.');
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

        return redirect()->route('admin.settings.index')
            ->with('success', 'Security password changed successfully.');
    }

    public function updateGcash(Request $request)
    {
        $validated = $request->validate([
            'gcash_name' => 'required|string|max:255',
            'gcash_number' => 'required|string|max:50',
            'gcash_qr_code' => 'nullable|file|mimes:jpeg,png,jpg,webp,svg|max:5120',
        ]);

        Setting::set('gcash_name', $validated['gcash_name']);
        Setting::set('gcash_number', $validated['gcash_number']);

        if ($request->hasFile('gcash_qr_code')) {
            $oldPath = Setting::get('gcash_qr_path');
            if ($oldPath && !str_starts_with($oldPath, 'settings/default')) {
                FileUploadService::delete($oldPath);
            }
            $path = FileUploadService::store($request->file('gcash_qr_code'), 'settings');
            Setting::set('gcash_qr_path', $path);
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'GCash payment information and QR code updated successfully.');
    }
}
