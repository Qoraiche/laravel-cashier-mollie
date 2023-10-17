<?php

namespace Cashier\Mollie\Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Cashier\Mollie\Tests\Fixtures\User;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
        ];
    }
}
