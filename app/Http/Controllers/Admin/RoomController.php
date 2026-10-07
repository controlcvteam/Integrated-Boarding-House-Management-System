<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $query = Room::with(['primaryImage', 'activeTenants'])->withCount('activeTenants');

        if ($request->filled('search')) {
            $query->searchRelated($request->search);
        }

        if ($request->filled('room_type')) {
            $query->where('room_type', $request->room_type);
        }

        if ($request->filled('floor')) {
            $query->where('floor', $request->floor);
        }

        if ($request->filled('status')) {
            if ($request->status === 'available') {
                $query->where('manual_available', true)
                      ->havingRaw('active_tenants_count < capacity');
            } elseif ($request->status === 'occupied') {
                $query->havingRaw('active_tenants_count >= capacity');
            } elseif ($request->status === 'manual_closed') {
                $query->where('manual_available', false);
            }
        }

        $rooms = $query->orderBy('room_number')->paginate(10)->withQueryString();

        $roomTypes = Room::select('room_type')->distinct()->pluck('room_type');
        $floors = Room::select('floor')->whereNotNull('floor')->distinct()->pluck('floor');

        return view('admin.rooms.index', compact('rooms', 'roomTypes', 'floors'));
    }

    public function create()
    {
        $standardAmenities = [
            'Air Conditioner',
            'Private Bathroom',
            'Bed Frame',
            'Closet',
            'Wi-Fi Access',
            'Study Desk',
            'Water Heater',
        ];

        return view('admin.rooms.create', compact('standardAmenities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_number' => ['required', 'string', 'max:50', 'unique:rooms,room_number'],
            'room_name' => ['nullable', 'string', 'max:100'],
            'room_type' => ['required', 'string', 'max:100'],
            'capacity' => ['required', 'integer', 'min:1'],
            'monthly_rent' => ['required', 'numeric', 'min:1'],
            'floor' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'amenities' => ['nullable', 'array'],
            'manual_available' => ['nullable', 'boolean'],
            'images.*' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ]);

        $room = Room::create([
            'room_number' => $validated['room_number'],
            'room_name' => $validated['room_name'] ?? null,
            'room_type' => $validated['room_type'],
            'capacity' => $validated['capacity'],
            'monthly_rent' => $validated['monthly_rent'],
            'floor' => $validated['floor'] ?? null,
            'description' => $validated['description'] ?? null,
            'amenities' => $validated['amenities'] ?? [],
            'manual_available' => $request->has('manual_available'),
        ]);

        if ($request->hasFile('images')) {
            $isFirst = true;
            foreach ($request->file('images') as $file) {
                $path = $file->store('rooms', 'public');
                RoomImage::create([
                    'room_id' => $room->id,
                    'image_path' => $path,
                    'is_primary' => $isFirst,
                ]);
                $isFirst = false;
            }
        }

        return redirect()->route('admin.rooms.show', $room->id)
            ->with('success', 'Room ' . $room->room_number . ' created successfully!');
    }

    public function show($id)
    {
        $room = ($id instanceof Room && $id->exists) ? $id : Room::findOrFail($id);
        $room->load(['images', 'activeTenants.user', 'roomRequests.tenant']);
        return view('admin.rooms.show', compact('room'));
    }

    public function edit($id)
    {
        $room = ($id instanceof Room && $id->exists) ? $id : Room::findOrFail($id);
        $standardAmenities = [
            'Air Conditioner',
            'Private Bathroom',
            'Bed Frame',
            'Closet',
            'Wi-Fi Access',
            'Study Desk',
            'Water Heater',
        ];

        return view('admin.rooms.edit', compact('room', 'standardAmenities'));
    }

    public function update(Request $request, $id)
    {
        $room = ($id instanceof Room && $id->exists) ? $id : Room::findOrFail($id);
        $validated = $request->validate([
            'room_number' => ['required', 'string', 'max:50', Rule::unique('rooms')->ignore($room->id)],
            'room_name' => ['nullable', 'string', 'max:100'],
            'room_type' => ['required', 'string', 'max:100'],
            'capacity' => ['required', 'integer', 'min:1'],
            'monthly_rent' => ['required', 'numeric', 'min:1'],
            'floor' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'amenities' => ['nullable', 'array'],
            'manual_available' => ['nullable', 'boolean'],
            'images.*' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ]);

        $room->update([
            'room_number' => $validated['room_number'],
            'room_name' => $validated['room_name'] ?? null,
            'room_type' => $validated['room_type'],
            'capacity' => $validated['capacity'],
            'monthly_rent' => $validated['monthly_rent'],
            'floor' => $validated['floor'] ?? null,
            'description' => $validated['description'] ?? null,
            'amenities' => $validated['amenities'] ?? [],
            'manual_available' => $request->has('manual_available'),
        ]);

        if ($request->hasFile('images')) {
            $hasExistingPrimary = $room->images()->where('is_primary', true)->exists();
            $isFirst = !$hasExistingPrimary;
            foreach ($request->file('images') as $file) {
                $path = $file->store('rooms', 'public');
                RoomImage::create([
                    'room_id' => $room->id,
                    'image_path' => $path,
                    'is_primary' => $isFirst,
                ]);
                $isFirst = false;
            }
        }

        return redirect()->route('admin.rooms.show', $room->id)
            ->with('success', 'Room ' . $room->room_number . ' updated successfully!');
    }

    public function uploadImages(Request $request, $id)
    {
        $room = ($id instanceof Room && $id->exists) ? $id : Room::findOrFail($id);
        $request->validate([
            'images.*' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ]);

        if ($request->hasFile('images')) {
            $hasExistingPrimary = $room->images()->where('is_primary', true)->exists();
            $isFirst = !$hasExistingPrimary;
            foreach ($request->file('images') as $file) {
                $path = $file->store('rooms', 'public');
                RoomImage::create([
                    'room_id' => $room->id,
                    'image_path' => $path,
                    'is_primary' => $isFirst,
                ]);
                $isFirst = false;
            }
        }

        return back()->with('success', 'Photos uploaded successfully.');
    }

    public function destroy($id)
    {
        $room = ($id instanceof Room && $id->exists) ? $id : Room::findOrFail($id);

        if ($room->activeTenants()->count() > 0) {
            return back()->with('error', 'Cannot delete Room ' . $room->room_number . ' because it currently has active occupants. Reassign occupants before deleting.');
        }

        // Delete associated image files from storage
        foreach ($room->images as $img) {
            Storage::disk('public')->delete($img->image_path);
        }

        $roomNumber = $room->room_number;
        $room->delete();

        return redirect()->route('admin.rooms.index')
            ->with('success', 'Room ' . $roomNumber . ' was successfully deleted.');
    }

    public function toggleAvailability($id)
    {
        $room = ($id instanceof Room && $id->exists) ? $id : Room::findOrFail($id);
        $room->manual_available = !$room->manual_available;
        $room->save();

        $status = $room->manual_available ? 'marked as Available' : 'marked as Not Available (Closed)';
        return back()->with('success', 'Room ' . $room->room_number . ' ' . $status . '.');
    }

    public function setPrimaryImage($roomId, $imageId)
    {
        $room = ($roomId instanceof Room && $roomId->exists) ? $roomId : Room::findOrFail($roomId);
        $image = ($imageId instanceof RoomImage && $imageId->exists) ? $imageId : RoomImage::findOrFail($imageId);
        $room->images()->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);

        return back()->with('success', 'Primary photo updated.');
    }

    public function deleteImage($roomId, $imageId)
    {
        $room = ($roomId instanceof Room && $roomId->exists) ? $roomId : Room::findOrFail($roomId);
        $image = ($imageId instanceof RoomImage && $imageId->exists) ? $imageId : RoomImage::findOrFail($imageId);
        Storage::disk('public')->delete($image->image_path);
        $wasPrimary = $image->is_primary;
        $image->delete();

        if ($wasPrimary) {
            $newPrimary = $room->images()->first();
            if ($newPrimary) {
                $newPrimary->update(['is_primary' => true]);
            }
        }

        return back()->with('success', 'Photo removed successfully.');
    }
}
