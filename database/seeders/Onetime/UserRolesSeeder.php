<?php

namespace Database\Seeders\Onetime;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userAdmin = User::whereNull('merchant_id')->get();
        $userMerchant = User::whereNotNull('merchant_id')->get();

        $userAdmin->map(function (User $user) {
            $user->assignRole('admin');
        });

        $userMerchant->map(function (User $user) {
            $user->assignRole('merchant');
        });
    }
}
