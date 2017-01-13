<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        DB::table('users')->truncate();
        User::create([
            'name' => 'Hermann Donfack', 
            'email' => 'donfack.hermann@job.io', 
            'password' => Hash::make('secret'), 
            'registration_completed' => true,
            'remember_token' => str_random(10),
        ]);
        Model::reguard();
    }
}
