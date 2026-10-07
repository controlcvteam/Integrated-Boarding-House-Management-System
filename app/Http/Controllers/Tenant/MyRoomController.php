<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\RoomRequest;
use Illuminate\Http\Request;

class MyRoomController extends Controller
{
    public function index()
    {
        $tenant = auth()->user()->tenant;

        if (!$tenant || !$tenant->room) {
            return redirect()->route('tenant.rooms.index')
                ->with('warning', 'You do not have an assigned room yet. Please browse available rooms and submit a room request.');
        }

        $room = $tenant->room->load('images');

        $activeRequest = RoomRequest::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->first();

        return view('tenant.my-room.index', compact('tenant', 'room', 'activeRequest'));
    }
}
