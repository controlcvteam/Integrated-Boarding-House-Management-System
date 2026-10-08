<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class PublicRoomController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $floor = $request->query('floor');
        $type = $request->query('type');
        $status = $request->query('status', $request->query('availability'));

        $query = Room::with(['images', 'activeTenants'])
            ->withCount('activeTenants')
            ->searchRelated($search)
            ->when($floor, function ($q, $floor) {
                $q->where(function ($sub) use ($floor) {
                    $sub->where('floor', $floor)
                        ->orWhere('floor', 'like', "%{$floor}%");
                });
            })
            ->when($type, function ($q, $type) {
                $q->where('room_type', $type);
            })
            ->when($status, function ($q, $status) {
                if ($status === 'available') {
                    $q->where('manual_available', true)
                      ->whereRaw('(SELECT COUNT(*) FROM tenants WHERE tenants.room_id = rooms.id AND tenants.status = "active") < rooms.capacity');
                } elseif ($status === 'occupied') {
                    $q->where(function ($occQ) {
                        $occQ->where('manual_available', false)
                             ->orWhereRaw('(SELECT COUNT(*) FROM tenants WHERE tenants.room_id = rooms.id AND tenants.status = "active") >= rooms.capacity');
                    });
                }
            })
            ->orderBy('room_number');

        $rooms = $query->paginate(12)->withQueryString();

        $floors = cache()->remember('public_room_floors', 300, function () {
            return Room::distinct()->whereNotNull('floor')->pluck('floor')->sort()->values();
        });
        $roomTypes = cache()->remember('public_room_types', 300, function () {
            return Room::distinct()->whereNotNull('room_type')->pluck('room_type')->sort()->values();
        });

        return view('public.rooms', compact('rooms', 'floors', 'roomTypes', 'search', 'floor', 'type', 'status'));
    }
}
