<?php

namespace Database\Factories;

use App\Models\Reserve;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Room;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reserve>
 */
class ReserveFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Reserve::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $roomsIds = Room::pluck('id')->toArray();
        $usersIds = User::pluck('id')->toArray();
        return [
            "room_id" => $this->faker->randomElement($roomsIds),
            "user_id" => $this->faker->randomElement($usersIds),
            "title" => $this->faker->title(),
            "name" => $this->faker->name(),
            "start_time" => $this->faker->dateTimeBetween('now', '+10 days')->format('Y-m-d H:i:s'),
            "stop_time" => $this->faker->dateTimeBetween('+10 days', '+10 days')->format('Y-m-d H:i:s'),
            "participant" => $this->faker->randomElement($usersIds),
            "permission_status" => $this->faker->numberBetween(0, 2)
        ];
    }
}