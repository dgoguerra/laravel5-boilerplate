<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Hashing\BcryptHasher;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $hasher = new BcryptHasher;

        // create test users

        $user1 = new User;
        $user1->name = 'John';
        $user1->email = 'user1@mail.com';
        $user1->password = $hasher->make('secret');
        $user1->is_active = 1;
        $user1->save();

        $user2 = new User;
        $user2->name = 'Peter';
        $user2->email = 'user2@mail.com';
        $user2->password = $hasher->make('secret');
        $user2->is_active = 1;
        $user2->save();
    }
}
