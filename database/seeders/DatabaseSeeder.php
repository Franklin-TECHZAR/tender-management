<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\CompanySetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $company_settings = new CompanySetting();
        $company_settings->name = "Alpha Power Electromechanical Projects";
        $company_settings->mobile = "9876543210";
        $company_settings->email = "admin@gmail.com";
        $company_settings->address = "Trichy, Tamilnadu";
        $company_settings->gst_number = "0000000000000000";
        $company_settings->save();

        $permissions = new Permission();
        $permissions->name = 'add-user';
        $permissions->save();

        $permissions = new Permission();
        $permissions->name = 'edit-user';
        $permissions->save();

        $permissions = new Permission();
        $permissions->name = 'delete-user';
        $permissions->save();

        $permissions = new Permission();
        $permissions->name = 'view-user';
        $permissions->save();

        $roles = new Role();
        $roles->name = "Admin";
        $roles->save();
        $roles->syncPermissions([1,2,3,4]);

        $user = new User();
        $user->name = "Admin";
        $user->email = "admin@gmail.com";
        $user->password = Hash::make("admin@123");
        $user->role_id = 1;
        $user->save();
    }
}
