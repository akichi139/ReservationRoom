<?php

namespace Tests\Unit;

use App\Mail\ChangeStatusResponseMail;
use App\Mail\ResponseMail;
use Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use App\Models\Reserve;
use App\Models\User;
use App\Models\Room;

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

    public function test_room_require_permission_Change_Status_To_Cancel_Using_ChangeStatusResponseMail()
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
        $response = $this->actingAs($user)->get("/reserve_update_status/$reserve->id/2");

        $response->assertRedirect('/reserve');
        Mail::assertSent(ChangeStatusResponseMail::class, function ($mail) use ($reserve) {
            return $mail->hasTo($reserve->user->email);
        });
    }

    public function test_room_require_permission_Change_Status_To_Approval_Using_ChangeStatusResponseMail()
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
        $response = $this->actingAs($user)->get("/reserve_update_status/$reserve->id/0");

        $response->assertRedirect('/reserve');
        Mail::assertSent(ChangeStatusResponseMail::class, function ($mail) use ($reserve) {
            return $mail->hasTo($reserve->user->email);
        });
    }

    public function test_room_require_permission_Change_Status_To_Pending_Status_not_change()
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
        $response = $this->actingAs($user)->get("/reserve_update_status/$reserve->id/1");
        $response->assertRedirect('/reserve');
    }

    public function test_room_not_require_permission_Change_Status_To_Cancel_Using_ChangeStatusResponseMail()
    {
        Mail::fake();
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
        $response = $this->actingAs($user)->get("/reserve_update_status/$reserve->id/2");

        $response->assertRedirect('/reserve');
        Mail::assertSent(ChangeStatusResponseMail::class, function ($mail) use ($reserve) {
            return $mail->hasTo($reserve->user->email);
        });
    }

    public function test_room_not_require_permission_Change_Status_To_Approval_status_not_change()
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
        $response = $this->actingAs($user)->get("/reserve_update_status/$reserve->id/0");

        $response->assertRedirect('/reserve');
    }

    public function test_room_not_require_permission_Change_Status_To_Pending_reserve_status_cannot_change_to_pending()
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
        $response = $this->actingAs($user)->get("/reserve_update_status/$reserve->id/1");

        $response->assertRedirect('/reserve');
    }

    public function test_mail_sent_to_admin_when_reserve_is_pending()
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
        Mail::assertSent(ResponseMail::class, function ($mail) use ($reserve) {
            return $mail->hasTo("admin@gmail.com");
        });
    }
}