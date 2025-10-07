<?php

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Role::create([
            'role_name' => 'administrator',
        ]);

        Role::create([
            'role_name' => 'surveyorin',
        ]);
        
        Role::create([
            'role_name' => 'surveyorout',
        ]);

        Role::create([
            'role_name' => 'adminops',
        ]);

        Role::create([
            'role_name' => 'ops',
        ]);

        Role::create([
            'role_name' => 'chasier',
        ]);

        User::factory(6)->create();
    }
}
