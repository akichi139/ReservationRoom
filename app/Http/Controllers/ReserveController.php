<?php

namespace App\Http\Controllers;

use App\Events\PermissionStatusChanged;
use App\Http\Requests\ValidateReserve;
use App\Models\Reserve;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Mail\PendingMail;
use Spatie\Period\Boundaries;
use Spatie\Period\Period;
use Spatie\Period\Precision;
use App\Mail\ResponseMail;
use Illuminate\Support\Facades\Mail;

class ReserveController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $date = $request->input('date');
        $thisMonth = $request->input('this_month');

        $query = Reserve::query();

        if ($search) {
            $query->where('title', 'LIKE', "%$search%");
        }

        if ($date) {
            $query->whereDate('start_time', '=', $date);
        }

        if ($thisMonth) {
            $query->whereMonth('start_time', '=', date('m'))
                ->whereYear('start_time', '=', date('Y'));
        }

        $query->orderBy('start_time', 'ASC');

        $data['Reservation'] = $query->paginate(5);

        return view('reserve.index', $data);
    }

    public function create()
    {
        return view('reserve.create');
    }

    public function store(ValidateReserve $request)
    {
        // dd($request);
        $temp = [];
        $starttemp = Carbon::parse("{$request->date} {$request->start_time}");
        $stoptemp = Carbon::parse("{$request->repeatTime} {$request->stop_time}");
        $start = Carbon::parse("{$request->date} {$request->start_time}");
        $stop = Carbon::parse("{$request->date} {$request->stop_time}");
        $particapant = $request->input('participant');
        $room = Room::find($request->room_id);
        // dd($request);
        if (count($particapant) > $room->max_participant) {
            return redirect()->route('timeslots')->with('time_error', 'Too many participant for a convention room. please choose another convention room.');
        }
        $participant_str = implode(',', $particapant);
        //check period from db
        $checkperiod = Reserve::where('room_id', $request->room_id)->where('permission_status', '!=', 2)->get();
        foreach ($checkperiod as $period) {
            $a = Period::make($period->start_time, $period->stop_time, Precision::HOUR(), boundaries: Boundaries::EXCLUDE_END());
            $b = Period::make($start->format('Y-m-d H:i:s'), $stop->format('Y-m-d H:i:s'), Precision::HOUR(), boundaries: Boundaries::EXCLUDE_END());
            if (($a->overlapsWith($b)) == true) {
                return redirect()->route('timeslots')->with('time_error', 'The selected time has already been reserved. please try again.');
            }
        }
        //default status
        $permission_status = 0;
        if ($room->admin_permission == 1) {
            $permission_status = 1;
        }

        $reserve = new Reserve;
        $reserve->title = $request->title;
        $reserve->name = $request->name;
        $reserve->room_id = $request->room_id;
        $reserve->start_time = $start;
        $reserve->stop_time = $stop;
        $reserve->participant = $participant_str;
        $reserve->permission_status = $permission_status;

        if ($request->toggle == 'on') {
            for ($i = $starttemp; $i <= $stoptemp; $i->addDays(7)) {
                foreach ($checkperiod as $period) {
                    $a = Period::make($period->start_time, $period->stop_time, Precision::HOUR(), boundaries: Boundaries::EXCLUDE_END());
                    $b = Period::make($start->format('Y-m-d H:i:s'), $stop->format('Y-m-d H:i:s'), Precision::HOUR(), boundaries: Boundaries::EXCLUDE_END());
                    if (($a->overlapsWith($b)) == true) {
                        return redirect()->route('timeslots')->with('time_error', 'some repeat time has already been reserved. please try again.');
                    }
                }
                $reserve = new Reserve;
                $reserve->title = $request->title;
                $reserve->name = $request->name;
                $reserve->room_id = $request->room_id;
                $reserve->start_time = $start->format('Y-m-d H:i:s');
                $reserve->stop_time = $stop->format('Y-m-d H:i:s');
                $reserve->participant = $participant_str;
                $reserve->permission_status = $permission_status;
                $reserve->save();

                $start->addDays(7);
                $stop->addDays(7);
            }
            if ($permission_status == 1) {
                return redirect()->route('room.index')->with('success', 'Pending mail has been sent successfully.');
            }
            return redirect()->route('room.index')->with('success', 'Repeat reserve has been created successfully.');
        }

        //create data
        $reserve->save();

        if ($permission_status == 1) {
            return redirect()->route('room.index')->with('success', 'Pending mail has been sent successfully.');
        }
        return redirect()->route('room.index')->with('success', 'Reserve has been created successfully.');
    }

    public function edit(Reserve $reserve)
    {
        $employees = User::orderBy('id', 'asc')->get();
        return view('reserve.edit', compact('reserve', 'employees'));
    }

    public function update(ValidateReserve $request, $id)
    {
        // dd($request);
        $start_str = Carbon::parse("{$request->date} {$request->start_time}")->format('Y-m-d H:i:s');
        $stop_str = Carbon::parse("{$request->date} {$request->stop_time}")->format('Y-m-d H:i:s');
        $particapant = $request->input('participant');
        $room = Room::find($request->room_id);
        // dd($room->max_participant);
        if (count($particapant) > $room->max_participant) {
            return redirect()->route('timeslots')->with('time_error', 'Too many participant for a convention room. please choose another convention room.');
        }
        $participant_str = implode(',', $particapant);
        //check period from db
        $checkperiod = Reserve::where('room_id', $request->room_id)->where('permission_status', '!=', 2)->get();
        foreach ($checkperiod as $period) {
            $a = Period::make($period->start_time, $period->stop_time, Precision::HOUR(), boundaries: Boundaries::EXCLUDE_END());
            $b = Period::make($start_str, $stop_str, Precision::HOUR(), boundaries: Boundaries::EXCLUDE_END());
            if (($a->overlapsWith($b)) == true) {
                return redirect()->route('timeslots')->with('time_error', 'The selected time has already been reserved. please try again.');
            }
        }
        //default status
        $permission_status = 0;
        if ($room->admin_permission == 1) {
            $permission_status = 1;
        }

        $reserve = Reserve::find($id);
        $reserve->title = $request->title;
        $reserve->name = $request->name;
        $reserve->room_id = $request->room_id;
        $reserve->start_time = $start_str;
        $reserve->stop_time = $stop_str;
        $reserve->participant = $participant_str;
        $reserve->permission_status = $permission_status;
        $reserve->save();

        return redirect()->route('room.index')->with('success', 'Reserve has been update successfully.');
    }

    public function destroy(Reserve $reserve)
    {
        $reserve->delete();

        return redirect()->route('reserve.index')->with('success', 'Reserve has been delete successfully.');
    }

    public function indextimeslot(Request $request, $date = null)
    {
        if (is_null($date)) {
            $date = Carbon::now();
        } else {
            $date = Carbon::parse($date);
        }
        $start = $date->startOfWeek(Carbon::MONDAY);
        $weekStartDate = $start->startOfWeek()->format('Y-m-d H:i');
        $tempdate = [];
        for ($i = 0; $i < 5; $i++) {
            $days_ago = gmdate('d-m-Y', strtotime("+$i days", strtotime($weekStartDate)));
            array_push($tempdate, $days_ago);
        }
        $start_time = $date->format('Y-m-d H:i:s');
        $stop_time = date('Y-m-d', strtotime($start_time . ' +6 day'));
        $Reservations = Reserve::where('start_time', '>=', $start_time)->where('stop_time', '<=', $stop_time)->where('permission_status', '!=', 2)->get();
        $Days = $tempdate;
        $Rooms = Room::orderBy('id', 'asc')->get();

        return view('reserve.timeslot', compact('Reservations', 'Days', 'Rooms'));
    }

    public function changePermissionStatus(Request $request)
    {
        $reserve = Reserve::find($request->id);
        $reserve->update(['permission_status' => $request->status]);
        if(($reserve->status == 0 || $reserve->status == 2) && $reserve->start_time > Carbon::now()->toDateTimeString()){
            event(new PermissionStatusChanged($reserve));
        }
        return redirect()->route('reserve.index')->with('success', 'Reserve has been update successfully.');
    }
}