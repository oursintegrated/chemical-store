<?php

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->truncate();

        DB::table('users')->insert([
            [
                'full_name' => 'Administrator',
                'username' => 'admin',
                'email' => 'admin@demo.com',
                'role_id' => 1,
                'password' => bcrypt('admin'),
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => Date('Y-m-d H:i:s'),
                'updated_at' => Date('Y-m-d H:i:s')
            ],
        ]);
    }
}
