<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;


class UserSeeder extends Seeder
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
            "role" => "ADMIN",

        ];
       
            User::create([
                "name" => $user["name"],
                "email" => $user["email"],
                "password" => $user["password"],
                "role" => $user["role"],
            ]);
        }
    }

