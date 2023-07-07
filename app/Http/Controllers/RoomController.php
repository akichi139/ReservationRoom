<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidateRoom;
use App\Models\Reserve;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use stdClass;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $data_room['Room'] = Room::orderBy('id', 'asc')->paginate(5);

        return view('room.index', $data_room);
    }

    public function getCalendaEvents(Request $request)
    {
        $events = Reserve::orderBy('start_time', 'asc')->where('permission_status', '!=', 2)->get();
        $json_event = [];
        //loop data orderby asc with start_time in Reserve
        /** @var \App\Models\Reserve $event */
        foreach ($events as $event) {
            $participantIDArray = explode(',', $event->participant);
            $participantNameArray = [];
            //loop data from id in event->participant
            foreach ($participantIDArray as $participantID) {
                $participant = User::find($participantID);
                array_push($participantNameArray, $participant->name);
            }
            $strParticipantName = implode(', ', $participantNameArray);
            $arr_event = ['title' => $event->name.' '.date('H:i', strtotime($event->start_time)).'-'.date('H:i', strtotime($event->stop_time)), 'start' => $event->start_time, 'end' => $event->stop_time, 'color' => $event->room->color, 'participant' => $strParticipantName];
            array_push($json_event, $arr_event);
        }

        return json_encode($json_event);
    }

    public function create()
    {
        return view('room.create');
    }

    public function store(ValidateRoom $request)
    {
        $fileName = time().'.'.$request->image->extension();
        $request->image->storeAs('public/images', $fileName);

        $room = new Room;
        $room->room_name = $request->name;
        $room->color = $request->color;
        $room->max_participant = $request->max_participant;
        $room->image = $fileName;
        $room->admin_permission = $request->admin_permission == 'on' ? true : false;
        $room->save();

        return redirect()->route('room.index')->with('success', 'Room has been created successfully.');
    }

    public function edit(Room $room)
    {
        return view('room.edit', compact('room'));
    }

    public function update(ValidateRoom $request, $id)
    {
        $fileName = time().'.'.$request->image->extension();
        $request->image->storeAs('public/images', $fileName);

        $room = Room::find($id);
        $room->room_name = $request->name;
        $room->color = $request->color;
        $room->max_participant = $request->max_participant;
        $room->image = $fileName;
        $room->admin_permission = $request->admin_permission == 'on' ? 1 : 0;
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
        $employees = User::orderBy('id', 'asc')->get();
        $data = new stdClass;
        $data->date = $_REQUEST['date'];
        $data->start_time = $_REQUEST['start_time'];
        $data->stop_time = $_REQUEST['stop_time'];

        return view('reserve.create', compact('room', 'data', 'employees'));
    }
}
