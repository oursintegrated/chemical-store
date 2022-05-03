<?php

use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('roles')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('roles')->insert([
            [
                'name' => 'administrator',
                'display_name' => 'Administrator',
                'description' => 'Administrator get full access.',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => Date('Y-m-d H:i:s'),
                'updated_at' => Date('Y-m-d H:i:s')
            ],
        ]);
    }
}
