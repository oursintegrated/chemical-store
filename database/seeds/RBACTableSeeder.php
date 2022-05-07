<?php

use Illuminate\Database\Seeder;

class RBACTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('rbac')->truncate();

        DB::table('rbac')->insert([
            [
                'menu_id' => 1,
                'key' => 'dashboard.index',
            ],
            [
                'menu_id' => 4,
                'key' => 'user.index',
            ],
            [
                'menu_id' => 5,
                'key' => 'role.index',
            ],
            [
                'menu_id' => 6,
                'key' => 'user.create',
            ],
            [
                'menu_id' => 7,
                'key' => 'user.edit',
            ],
            [
                'menu_id' => 8,
                'key' => 'user.destroy',
            ],
            [
                'menu_id' => 9,
                'key' => 'role.create',
            ],
            [
                'menu_id' => 10,
                'key' => 'role.edit',
            ],
            [
                'menu_id' => 11,
                'key' => 'role.destroy',
            ],
            [
                'menu_id' => 12,
                'key' => 'product.index',
            ],
            [
                'menu_id' => 13,
                'key' => 'product.create',
            ],
            [
                'menu_id' => 14,
                'key' => 'product.edit',
            ],
            [
                'menu_id' => 15,
                'key' => 'product.destroy',
            ],
            [
                'menu_id' => 16,
                'key' => 'customer.index',
            ],
            [
                'menu_id' => 17,
                'key' => 'customer.create',
            ],
            [
                'menu_id' => 18,
                'key' => 'customer.edit',
            ],
            [
                'menu_id' => 19,
                'key' => 'customer.destroy',
            ],
            [
                'menu_id' => 20,
                'key' => 'sales.index',
            ],
            [
                'menu_id' => 21,
                'key' => 'sales.create',
            ],
            [
                'menu_id' => 22,
                'key' => 'sales.destroy',
            ],
            [
                'menu_id' => 23,
                'key' => 'product.recipe',
            ],
            [
                'menu_id' => 24,
                'key' => 'recipe.index',
            ],
            [
                'menu_id' => 25,
                'key' => 'recipe.create',
            ],
            [
                'menu_id' => 26,
                'key' => 'recipe.edit',
            ],
            [
                'menu_id' => 27,
                'key' => 'recipe.destroy',
            ],
        ]);
    }
}
