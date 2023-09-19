<?php

namespace App\Console\Commands;

use App\Actions\Fortify\PasswordValidationRules;
use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CreateSuperAdmin extends Command
{
    use PasswordValidationRules;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-super-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create app super admin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $super_admin_role = Role::firstOrCreate([
            'name' => 'super-admin',
            'store_id'=> null
        ]);

        if($super_admin_role->users->count() > 0){
            $this->warn('A super admin has already been created');
            return ;
        }

        $input = [];
        $input['first_name'] = $this->ask('First Name');
        $input['last_name'] = $this->ask('Last Name');
        $input['email'] = $this->ask('Email');

        $this->warn('Securely enter your password below');
        $input['password'] = $this->secret('Password ');
        $input['password_confirmation'] = $this->secret('Confirm Password');

        $validator = Validator::make($input, [
            'first_name' => ['required', 'string', 'max:255'],
            'first_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => $this->passwordRules(),
        ]);

        if($validator->fails()){
            $this->error($validator->errors());
            return ;
        }


        $input['password'] = Hash::make($input['password']);
        if(is_null($user = User::where('email', $input['email'])->first())){
            $user = new User();
        }

        $user->forceFill([
            'first_name' =>  $input['first_name'],
            'last_name' => $input['last_name'],
            'email' => $input['email'],
            'password' => $input['password'],
        ]);
        $user->email_verified_at = now();
        $user->save();

        $user->roles()->attach($super_admin_role->id);

        if($user->hasRole($super_admin_role)){
            $this->info('Super admin created successfully');
        }
        else {
            $this->fail('Failed! Super admin could not be created');
        }

    }
}
