<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class User extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = [
            "name" => "Admin",
            "email" => "admin@example.com",
            "password" => bcrypt("password123"),

        ];
        foreach ($user as $key => $value) {
            User::create([
                "name" => $value["name"],
                "email" => $value["email"],
                "password" => $value["password"],
            ]);
        }
    }
}
