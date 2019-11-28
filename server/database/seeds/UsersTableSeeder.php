<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => Admin,
            'email' => 'admin@innotec.security',
            'password' => bcrypt('admin123'),
        ]);
    }
}
