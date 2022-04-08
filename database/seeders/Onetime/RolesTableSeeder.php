<?php

namespace Database\Seeders\Onetime;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = Role::create(['name'=>'admin']);
        $executor = Role::create(['name'=>'executor']);
        $merchant = Role::create(['name'=>'merchant']);

        $adminPermissions = Permission::pluck('id','id')
            ->all();

        $executorPermissions = Permission::where('name', '!=', 'transaction-import')
            ->where('name', '!=', 'transaction-merchant-column')
            ->pluck('id','id')
            ->all();

        $merchantPermissions = Permission::where('name', 'transaction-import')
            ->pluck('id','id')
            ->all();

        $admin->syncPermissions($adminPermissions);
        $executor->syncPermissions($executorPermissions);
        $merchant->syncPermissions($merchantPermissions);
    }
}
