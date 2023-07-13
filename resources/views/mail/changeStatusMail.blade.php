<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Template</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.0.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ $reserve->title }}</div>
                    <div class="card-body">
                        <h3>Hello</h3>
                        <h4>Your HRM-Workplace convention room reserve pending<br></h4>
                        <pre>   Reserve Name: {{ $reserve->name }}<br>   Room Name: {{ $reserve->room->room_name }}<br>   Time period: {{ $reserve->start_time }} to {{ $reserve->stop_time }}<br>   Your reservation has been {{ $reserve->permission_status }}</pre>
                        <p>Thank you</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>