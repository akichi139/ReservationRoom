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
                    <div class="card-header">{{ $title }}</div>
                    <div class="card-body">
                        <h3>Hello</h3>
                        <h4>Your HRM-Workplace convention room reserve pending<br></h4>
                        <pre>   Reserve Name: {{ $reserve_name }}<br>   Room Name: {{ $room_name }}<br>   Time period: {{ $start_time }} to {{ $stop_time }}<br>   This reservation is waiting for approval</pre>
                        <p>Thank you</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>