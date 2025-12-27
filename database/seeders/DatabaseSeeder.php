<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Author;
use App\Enums\UserTypeEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ])->first();

        Author::create(
            [
                'name' => $user->name,
                'email' => $user->email,
                'user_id' => $user->id,
            ]
        );

        $roles = [
            UserTypeEnum::Admin->value,
            UserTypeEnum::User->value,
            UserTypeEnum::Editor->value,
            UserTypeEnum::Author->value
        ];
        // add roles to roles_table
        foreach ($roles as $role)
            Role::create(['name' => $role , 'guard_name' => 'api',]);

        $userRole = Role::where('name' ,'user')->first();
        // add role for the user

        $user->assignRole($userRole->id);
    }
}
