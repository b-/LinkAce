<?php

namespace Database\Factories;

use App\Actions\Settings\SetDefaultSettingsForUser;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->userName(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => '$2y$10$9.preebMjZ.8obdvk5ZVdOCw7Cq1EJm6i1B1RJevxCXYW0lUiwDJG', // secretpassword
        ];
    }

    public function configure(): UserFactory
    {
        return $this->afterCreating(function (User $user) {
            (new SetDefaultSettingsForUser($user))->up();
            $user->assignRole(Role::USER);
        });
    }
}
