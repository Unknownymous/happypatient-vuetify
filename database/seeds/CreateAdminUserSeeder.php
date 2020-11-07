<?php

use Illuminate\Database\Seeder;
use App\User;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
        	'name' => 'Admin Happypatient', 
            'email' => 'admin@happypatient.com',
            'username' => 'admin',
        	'password' => bcrypt('12345678')
        ]);

        // $role = Role::create(['name' => 'Admin']);

        // $permissions = Permission::pluck('id','id')->all();

        // $role->syncPermissions($permissions);

        // $user->assignRole([$role->id]);
    }
}
