<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Reserve;
use App\Models\Room;
use Tests\TestCase;

class ReserveCRUDTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_can_store_new_reserve_room_not_require_permission()
    {
        $user = User::factory()->create();
        $room = Room::create([
            'room_name' => 'convention1',
            'color' => '#ffffff',
            'max_participant' => 5,
            'image' => '0123456789.jpg',
            'admin_permission' => 0
        ]);
        $response = $this->actingAs($user)->post(route('reserve.store'), [
            'room_id' => $room->id,
            'title' => 'Meeting 1',
            'name' => $user->name,
            'date' => '2024-07-06',
            'start_time' => '16:30',
            'stop_time' => '19:00',
            'participant' => ['1', '2', '3', '4'],
        ]);
        $response->assertSessionHasNoErrors();
        $this->assertCount(1, Reserve::all());
        $this->assertDatabaseHas('reserves', [
            'room_id' => $room->id,
            'title' => 'Meeting 1',
            'name' => $user->name,
            'start_time' => '2024-07-06 16:30:00',
            'stop_time' => '2024-07-06 19:00:00',
            'participant' => '1,2,3,4',
            'permission_status' => 0
        ]);
        $response->assertRedirect('/room');
    }

    public function test_user_can_store_new_reserve_room_require_permission()
    {
        $user = User::factory()->create();
        $room = Room::create([
            'room_name' => 'convention1',
            'color' => '#ffffff',
            'max_participant' => 5,
            'image' => '0123456789.jpg',
            'admin_permission' => 1
        ]);
        $response = $this->actingAs($user)->post(route('reserve.store'), [
            'room_id' => $room->id,
            'title' => 'Meeting 1',
            'name' => $user->name,
            'date' => '2024-07-06',
            'start_time' => '16:30',
            'stop_time' => '19:00',
            'participant' => ['1', '2', '3', '4'],
        ]);
        $response->assertSessionHasNoErrors();
        $this->assertCount(1, Reserve::all());
        $this->assertDatabaseHas('reserves', [
            'room_id' => $room->id,
            'title' => 'Meeting 1',
            'name' => $user->name,
            'start_time' => '2024-07-06 16:30:00',
            'stop_time' => '2024-07-06 19:00:00',
            'participant' => '1,2,3,4',
            'permission_status' => 1
        ]);
        $response->assertRedirect('/room');
    }

    public function test_user_can_store_new_reserve_room_not_require_permission_with_repeat()
    {
        $user = User::factory()->create();
        $room = Room::create([
            'room_name' => 'convention1',
            'color' => '#ffffff',
            'max_participant' => 5,
            'image' => '0123456789.jpg',
            'admin_permission' => 0
        ]);
        $response = $this->actingAs($user)->post(route('reserve.store'), [
            'room_id' => $room->id,
            'title' => 'Meeting 1',
            'name' => $user->name,
            'date' => '2024-07-06',
            'start_time' => '16:30',
            'stop_time' => '19:00',
            'participant' => ['1', '2', '3', '4'],
            'toggle' => 'on',
            'repeatTime' => '2024-07-20'
        ]);
        $response->assertSessionHasNoErrors();
        $this->assertCount(3, Reserve::all());
        $this->assertDatabaseHas('reserves', [
            'room_id' => $room->id,
            'title' => 'Meeting 1',
            'name' => $user->name,
            'start_time' => '2024-07-13 16:30:00',
            'stop_time' => '2024-07-13 19:00:00',
            'participant' => '1,2,3,4',
            'permission_status' => 0
        ]);
        $response->assertRedirect('/room');
    }

    public function test_user_can_store_new_reserve_room_require_permission_with_repeat()
    {
        $user = User::factory()->create();
        $room = Room::create([
            'room_name' => 'convention1',
            'color' => '#ffffff',
            'max_participant' => 5,
            'image' => '0123456789.jpg',
            'admin_permission' => 1
        ]);
        $response = $this->actingAs($user)->post(route('reserve.store'), [
            'room_id' => $room->id,
            'title' => 'Meeting 1',
            'name' => $user->name,
            'date' => '2024-07-06',
            'start_time' => '16:30',
            'stop_time' => '19:00',
            'participant' => ['1', '2', '3', '4'],
            'toggle' => 'on',
            'repeatTime' => '2024-07-20'
        ]);
        $response->assertSessionHasNoErrors();
        $this->assertCount(3, Reserve::all());
        $this->assertDatabaseHas('reserves', [
            'room_id' => $room->id,
            'title' => 'Meeting 1',
            'name' => $user->name,
            'start_time' => '2024-07-13 16:30:00',
            'stop_time' => '2024-07-13 19:00:00',
            'participant' => '1,2,3,4',
            'permission_status' => 1
        ]);
        $response->assertRedirect('/room');
    }

    public function test_user_cannot_store_reserve_because_participant_more_than_room_max_participant()
    {
        $user = User::factory()->create();
        $room = Room::create([
            'room_name' => 'convention1',
            'color' => '#ffffff',
            'max_participant' => 5,
            'image' => '0123456789.jpg',
            'admin_permission' => 0
        ]);
        $response = $this->actingAs($user)->post(route('reserve.store'), [
            'room_id' => $room->id,
            'title' => 'Meeting 1',
            'name' => $user->name,
            'date' => '2024-07-06',
            'start_time' => '16:30',
            'stop_time' => '19:00',
            'participant' => ['1', '2', '3', '4', '5', '6'],
        ]);
        $response->assertSessionHasNoErrors();
        $this->assertCount(0, Reserve::all());
        $response->assertRedirect('/reserve_timeslot');
    }


    public function test_user_cannot_store_reserve_because_start_time_after_stop_time()
    {
        $user = User::factory()->create();
        $room = Room::create([
            'room_name' => 'convention1',
            'color' => '#ffffff',
            'max_participant' => 5,
            'image' => '0123456789.jpg',
            'admin_permission' => 0
        ]);
        $response = $this->actingAs($user)->post(route('reserve.store'), [
            'room_id' => $room->id,
            'title' => 'Meeting 1',
            'name' => $user->name,
            'date' => '2024-07-06',
            'start_time' => '19:30',
            'stop_time' => '19:00',
            'participant' => ['1', '2', '3', '4'],
        ]);
        $response->assertSessionHasErrors();
        $this->assertCount(0, Reserve::all());
    }
    
    public function test_user_can_edit_reserve()
    {
        Room::factory()->create();
        $user = User::factory()->create();
        $reserve = Reserve::factory()->create();
        $response = $this->actingAs($user)->get('/reserve/' . $reserve->id . '/edit');
        $response->assertStatus(200);
        $response->assertSee($reserve->title);
    }

    public function test_user_can_update_reserve()
    {
        $room = Room::factory()->create();
        $user = User::factory()->create();
        Reserve::factory()->create();
        $this->assertCount(1, Reserve::all());
        $reserve = Reserve::first();
        $response = $this->actingAs($user)->put('/reserve/' . $reserve->id, [
            'room_id' => $room->id,
            'title' => 'Meeting 1',
            'name' => $user->name,
            'date' => '2024-07-06',
            'start_time' => '16:30',
            'stop_time' => '19:00',
            'participant' => ['1', '2', '3', '4']
        ]);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/room');
        $this->assertEquals('Meeting 1', Reserve::first()->title);
    }

    public function test_user_can_update_reserve_status_from_pending_to_approval()
    {
        Room::create([
            'room_name' => 'convention1',
            'color' => '#ffffff',
            'max_participant' => 5,
            'image' => '0123456789.jpg',
            'admin_permission' => 1
        ]);
        $user = User::factory()->create();
        Reserve::factory()->create();
        $this->assertCount(1, Reserve::all());
        $reserve = Reserve::first();
        $response = $this->actingAs($user)->get('/reserve_update_status/' . $reserve->id . '/0' );
        $response->assertRedirect('/reserve');
        $this->assertEquals(0, Reserve::first()->permission_status);
    }

    public function test_user_can_update_reserve_status_from_pending_to_cancel()
    {
        Room::create([
            'room_name' => 'convention1',
            'color' => '#ffffff',
            'max_participant' => 5,
            'image' => '0123456789.jpg',
            'admin_permission' => 1
        ]);
        $user = User::factory()->create();
        Reserve::factory()->create();
        $this->assertCount(1, Reserve::all());
        $reserve = Reserve::first();
        $response = $this->actingAs($user)->get('/reserve_update_status/' . $reserve->id . '/2' );
        $response->assertRedirect('/reserve');
        $this->assertEquals(2, Reserve::first()->permission_status);
    }

    public function test_user_can_delete_reserve()
    {
        Room::factory()->create();
        $user = User::factory()->create();
        $reserve = Reserve::factory()->create();
        $this->assertEquals(1, Reserve::count());
        $response = $this->actingAs($user)->delete('/reserve/' . $reserve->id);
        $response->assertStatus(302);
        $this->assertEquals(0, Reserve::count());
    }
}