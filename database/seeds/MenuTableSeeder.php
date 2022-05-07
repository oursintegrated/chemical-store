<?php

use Illuminate\Database\Seeder;

class MenuTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('menus')->truncate();

        DB::table('menus')->insert([
            [
                'id' => 1,
                'menu_desc' => 'Dashboard',
                'parent_id' => null,
                'status' => 1,
                'level' => 1,
                'type' => 'menu',
            ],
            [
                'id' => 2,
                'menu_desc' => 'Data Master',
                'parent_id' => null,
                'status' => 1,
                'level' => 1,
                'type' => 'menu',
            ],
            [
                'id' => 3,
                'menu_desc' => 'Configuration',
                'parent_id' => null,
                'status' => 1,
                'level' => 1,
                'type' => 'menu',
            ],
            [
                'id' => 4,
                'menu_desc' => 'User',
                'parent_id' => 3,
                'status' => 1,
                'level' => 2,
                'type' => 'menu',
            ],
            [
                'id' => 5,
                'menu_desc' => 'Role',
                'parent_id' => 3,
                'status' => 1,
                'level' => 2,
                'type' => 'menu',
            ],
            [
                'id' => 6,
                'menu_desc' => 'User - Create',
                'parent_id' => 4,
                'status' => 1,
                'level' => 3,
                'type' => 'action',
            ],
            [
                'id' => 7,
                'menu_desc' => 'User - Edit',
                'parent_id' => 4,
                'status' => 1,
                'level' => 3,
                'type' => 'action',
            ],
            [
                'id' => 8,
                'menu_desc' => 'User - Delete',
                'parent_id' => 4,
                'status' => 1,
                'level' => 3,
                'type' => 'action',
            ],
            [
                'id' => 9,
                'menu_desc' => 'Role - Create',
                'parent_id' => 5,
                'status' => 1,
                'level' => 3,
                'type' => 'action',
            ],
            [
                'id' => 10,
                'menu_desc' => 'Role - Edit',
                'parent_id' => 5,
                'status' => 1,
                'level' => 3,
                'type' => 'action',
            ],
            [
                'id' => 11,
                'menu_desc' => 'Role - Delete',
                'parent_id' => 5,
                'status' => 1,
                'level' => 3,
                'type' => 'action',
            ],
            [
                'id' => 12,
                'menu_desc' => 'Product',
                'parent_id' => 2,
                'status' => 1,
                'level' => 2,
                'type' => 'menu',
            ],
            [
                'id' => 13,
                'menu_desc' => 'Product - Create',
                'parent_id' => 12,
                'status' => 1,
                'level' => 3,
                'type' => 'action',
            ],
            [
                'id' => 14,
                'menu_desc' => 'Product - Edit',
                'parent_id' => 12,
                'status' => 1,
                'level' => 3,
                'type' => 'action',
            ],
            [
                'id' => 15,
                'menu_desc' => 'Product - Delete',
                'parent_id' => 12,
                'status' => 1,
                'level' => 3,
                'type' => 'action',
            ],
            [
                'id' => 16,
                'menu_desc' => 'Customer',
                'parent_id' => 2,
                'status' => 1,
                'level' => 2,
                'type' => 'menu',
            ],
            [
                'id' => 17,
                'menu_desc' => 'Customer - Create',
                'parent_id' => 16,
                'status' => 1,
                'level' => 3,
                'type' => 'action',
            ],
            [
                'id' => 18,
                'menu_desc' => 'Customer - Edit',
                'parent_id' => 16,
                'status' => 1,
                'level' => 3,
                'type' => 'action',
            ],
            [
                'id' => 19,
                'menu_desc' => 'Customer - Delete',
                'parent_id' => 16,
                'status' => 1,
                'level' => 3,
                'type' => 'action',
            ],
            [
                'id' => 20,
                'menu_desc' => 'Sales',
                'parent_id' => null,
                'status' => 1,
                'level' => 1,
                'type' => 'menu',
            ],
            [
                'id' => 21,
                'menu_desc' => 'Sales - Create',
                'parent_id' => 20,
                'status' => 1,
                'level' => 2,
                'type' => 'action',
            ],
            [
                'id' => 22,
                'menu_desc' => 'Sales - Delete',
                'parent_id' => 20,
                'status' => 1,
                'level' => 2,
                'type' => 'action',
            ],
            [
                'id' => 23,
                'menu_desc' => 'Product - Recipe',
                'parent_id' => 12,
                'status' => 1,
                'level' => 3,
                'type' => 'action',
            ],
        ]);
    }
}
