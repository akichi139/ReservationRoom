<?php

namespace Tests\Unit;

use App\Mail\ChangeStatusResponseMail;
use App\Mail\ResponseMail;
use Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use App\Models\Reserve;
use App\Models\User;
use App\Models\Room;
use Illuminate\Support\Facades\Event;

class PendingMailTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_Sending_By_ResponseMail()
    {
        $user = User::factory()->create();
        $room = Room::create([
            'room_name' => 'convention1',
            'color' => '#ffffff',
            'max_participant' => 5,
            'image' => '0123456789.jpg',
            'admin_permission' => 0
        ]);
        $reserve = Reserve::create([
            'room_id' => $room->id,
            'user_id' => $user->id,
            'title' => 'Meeting 1',
            'name' => $user->name,
            'start_time' => '2024-07-06 16:30:00',
            'stop_time' => '2024-07-06 19:00:00',
            'participant' => '1,2,3,4',
            'permission_status' => 0
        ]);
        Mail::fake();
        Mail::assertNothingSent();
        Mail::to($reserve->user->email)->send(new ResponseMail($reserve));
        Mail::assertSent(ResponseMail::class);
    }

    public function test_Sending_By_ChangeStatusResponseMail()
    {
        $user = User::factory()->create();
        $room = Room::create([
            'room_name' => 'convention1',
            'color' => '#ffffff',
            'max_participant' => 5,
            'image' => '0123456789.jpg',
            'admin_permission' => 1
        ]);
        $reserve = Reserve::create([
            'room_id' => $room->id,
            'user_id' => $user->id,
            'title' => 'Meeting 1',
            'name' => $user->name,
            'start_time' => '2024-07-06 16:30:00',
            'stop_time' => '2024-07-06 19:00:00',
            'participant' => '1,2,3,4',
            'permission_status' => 1
        ]);
        Mail::fake();
        Mail::assertNothingSent();
        Mail::to('admin@gmail.com')->send(new ChangeStatusResponseMail($reserve));
        Mail::assertSent(ChangeStatusResponseMail::class);
    }

    public function test_reserve_permission_status_Change_Status_Using_ChangeStatusResponseMail()
    {
        Mail::fake();

        $user = User::factory()->create();
        $room = Room::create([
            'room_name' => 'convention1',
            'color' => '#ffffff',
            'max_participant' => 5,
            'image' => '0123456789.jpg',
            'admin_permission' => 1
        ]);
        $reserve = Reserve::create([
            'room_id' => $room->id,
            'user_id' => $user->id,
            'title' => 'Meeting 1',
            'name' => $user->name,
            'start_time' => '2024-07-06 16:30:00',
            'stop_time' => '2024-07-06 19:00:00',
            'participant' => '1,2,3,4',
            'permission_status' => 1
        ]);
        Event::fake();

        $response = $this->actingAs($user)->get("/reserve_update_status/$reserve->id/2");

        Mail::assertSent(ChangeStatusResponseMail::class, function ($mail) use ($reserve) {
            return $mail->reserve->id === $reserve->id;
        });

        Event::assertDispatched(PermissionStatusChanged::class, function ($event) use ($reserve) {
            return $event->reserve->id === $reserve->id;
        });
        $response->assertStatus(200);
        $response->assertRedirect('/reserve');
    }
}