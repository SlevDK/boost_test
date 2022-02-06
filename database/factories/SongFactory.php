<?php

namespace Database\Factories;

use App\Models\Song;
use Illuminate\Database\Eloquent\Factories\Factory;

class SongFactory extends Factory
{
    // Corresponding model
    protected $model = Song::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'email'     => $this->faker->email(),
            'name'      => $this->faker->name(),
            'ip'        => $this->faker->ipv4(),
            'duration'  => $this->faker->randomNumber(2),
            'total_duration' => $this->faker->randomNumber(4),
        ];
    }
}
