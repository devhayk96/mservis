<?php

namespace Database\Seeders\Onetime;

use App\Enums\PermissionsEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = PermissionsEnum::all();
        $permissionsAll = Permission::select('name')->pluck('name')->all();

        $checkedPermissions = array_diff($permissions, $permissionsAll);

        foreach ($checkedPermissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
