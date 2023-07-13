@extends('layouts.app')

@section('content')
<div class="container" mt-2>
    <div class="row">
        <div class="col-lg-12 text-center">
            <h2>Reserve Controll</h2>
        </div>
        <div>
            <a href="{{route('reserve.create', 'room_id')}}" class="btn btn-success mb-3">Reserve Room</a>
        </div>
        @if ($message=Session::get('success'))
        <div class="alert alert-success">
            <p> {{$message}}</p>
        </div>
        @endif
        <div class="row">
            <div class="col-lg-12">
                <form action="{{ route('reserve.index') }}" method="GET">
                    <div class="input-group mb-3">
                        <input type="text" name="search" class="form-control" placeholder="Search by Title..."
                            value="{{ request()->input('search') }}">
                        <input type="date" name="date" class="form-control" placeholder="Search by Date..."
                            value="{{ request()->input('date') }}">
                        <button class="btn btn-outline-secondary" type="submit">Search</button>
                        <button class="btn btn-outline-secondary" type="submit" name="this_month"
                            value="{{ now()->format('Y-m') }}">This Month</button>
                    </div>
                </form>
            </div>
        </div>
        <table class="table table-bordered">
            <tr>
                <th>No.</th>
                <th>Title</th>
                <th>Name</th>
                <th>Room Name</th>
                <th>Start Time</th>
                <th>Stop Time</th>
                <th>Participant</th>
                <th>Permission Status</th>
                <th width="280px">Action</th>
            </tr>
            @foreach($Reservation as $reserve)
            <tr>
                <td>{{$reserve->id}}</td>
                <td>{{$reserve->title}}</td>
                <td>{{$reserve->name}}</td>
                <td>{{$reserve->room->room_name}}</td>
                <td>{{$reserve->start_time}}</td>
                <td>{{$reserve->stop_time}}</td>
                <td>{{$reserve->participant}}</td>
                <td>
                    <select class="form-select" id="permission_status_{{$reserve->id}}" name="permission_status" <?php if ($reserve->start_time <= date('Y-m-d H:i:s',time()+25200)) echo 'disabled'; ?>>
                        <option value=0 @selected($reserve->permission_status == 0)>Approval</option>
                        <option value=1 @selected($reserve->permission_status == 1)>Pending</option>
                        <option value=2 @selected($reserve->permission_status == 2)>Cancel</option>
                    </select>
                    <button class="btn btn-primary mt-2" onclick="handleButtonClick({{ $reserve->id }})" <?php if ($reserve->start_time <= date('Y-m-d H:i:s',time()+25200)) echo 'disabled'; ?>>Change Status</button>
                </td>
                <td>
                    <form action="{{route('reserve.destroy', $reserve->id)}}" method="post">
                        <a href="{{route('reserve.edit', $reserve->id)}}" class="btn btn-warning">EDIT</a>
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">DELETE</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </table>
        {!!$Reservation->links('pagination::bootstrap-5')!!}
    </div>
</div>
<script>
    function handleButtonClick(reserveId) {
        let selectedValue = document.getElementById("permission_status_"+reserveId).value;
        window.location.href = '{{ route("updateReserveStatus", ["id" => "__id__", "status" => "__status__"]) }}'.replace('__id__', reserveId).replace('__status__', selectedValue);
    }
</script>
@endsection