<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesPermissionsTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ### Permissions ###

        # PERMISSIONS CACHE RESET #
        Artisan::call('permission:cache-reset');

        $permissions = [
            'ViewNova',
            'ViewRoles',
        ];

        $dashboard_permissions = [

        ];


        $permissions = array_merge($permissions);
        $permissions = array_unique($permissions);


        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['group' => 'Primary', 'name' => $permission]);
        }


        $dashboard_permissions = array_merge($dashboard_permissions);
        $dashboard_permissions = array_unique($dashboard_permissions);

        foreach ($dashboard_permissions as $permission) {
            Permission::firstOrCreate(['group' => 'Dashboard', 'name' => $permission]);
        }

        $collection = collect([
            'User',
            'UserPackage',
            'Video',
            'VideoPackage',
            'VideoRating',
            'Answer',
            'Category',
            'Character',
            'Difficulty',
            'Game',
            'GameQuestion',
            'Helper',
            'Package',
            'PackagePermission',
            'Question',
            'QuestionRating',
            'Ranking',
            'Setting',
        ]);

        $collection->each(function ($item, $key) {
            Permission::firstOrCreate(['group' => $item, 'name' => 'ViewAny-' . $item]);
            Permission::firstOrCreate(['group' => $item, 'name' => 'View-' . $item]);
            Permission::firstOrCreate(['group' => $item, 'name' => 'Create-' . $item]);
            Permission::firstOrCreate(['group' => $item, 'name' => 'Update-' . $item]);
            Permission::firstOrCreate(['group' => $item, 'name' => 'Restore-' . $item]);
            Permission::firstOrCreate(['group' => $item, 'name' => 'ForceDelete-' . $item]);
            Permission::firstOrCreate(['group' => $item, 'name' => 'Delete-' . $item]);
        });


        ### Roles ###

        $user = User::where('email', ['admin@admin.com'])->firstOrCreate([
            'name' => 'Admin',
            'password'=>Hash::make('password'),
            'email' => 'admin@admin.com'
        ]);

        $role = Role::firstOrCreate(['name' => 'admin']);

        if (!$role->exists) {
            $role->fill([
                'name' => 'admin',
            ])->save();
        }

        $role = Role::where('name', 'admin')->firstOrFail();

        $user->assignRole($role);

        // assign all permissions to role:admin
        $role->syncPermissions(Permission::all()->pluck('name'));

    }
}
