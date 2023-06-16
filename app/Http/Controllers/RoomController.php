<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Reserve;
use Illuminate\Http\Request;
use App\Http\Requests\ValidateRoom;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $data_room['Room'] = Room::orderBy('id', 'asc')->paginate(5);

        return view('room.index', $data_room);
    }

    public function getCalendaEvents(Request $request)
    {
        $events = Reserve::orderBy('start_time', 'asc')->get();
        $json_event=[];
        foreach ($events as $event) {
            $arr_event = ['title' => $event->name.' '.date("H:i", strtotime($event->start_time)).'-'.date("H:i", strtotime($event->stop_time)), 'start' => $event->start_time, 'end' => $event->stop_time, 'color' => $event->room->color];
            array_push($json_event,$arr_event);
        }
        return json_encode($json_event);
    }

    public function create()
    {
        return view('room.create');
    }

    public function store(ValidateRoom $request)
    {

        $room = new Room;
        $room->room_name = $request->name;
        $room->color= $request->color;
        $room->save();
        return redirect()->route('room.index')->with('success', 'Reserve has been created successfully.');
    }

    public function edit(Room $room)
    {
        return view('Room.edit', compact('room'));
    }

    public function update(ValidateRoom $request, $id)
    {

        $room = Room::find($id);
        $room->room_name = $request->name;
        $room->color= $request->color;
        $room->save();
        return redirect()->route('room.index')->with('success', 'Room has been update successfully.');
    }

    public function destroy(Room $room)
    {
        $room->delete();
        return redirect()->route('room.index')->with('success', 'Room has been delete successfully.');
    }

    public function reserve(Room $room)
    {
        return view('reserve.create', ['room_id' => $room->id]);
    }
}