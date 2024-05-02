<?php

namespace App\Console\Commands;

use App\Actions\Fortify\PasswordValidationRules;
use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class CreateAdmin extends Command
{
    use PasswordValidationRules;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create admin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {

            DB::beginTransaction();
            
            $admin = 'admin';

            $admin_count = User::whereHas('roles', function ($query) use ($admin) {
                $query->where('name', $admin);
            })->count();
    
            if($admin_count > 0){
               $this->warn('There\'s '. $admin_count .' admin(s) registered, press "Ctrl/Command + C" to terminate operation or ignore to cont.');
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
                'last_name' => ['nullable', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
                'password' => $this->passwordRules(),
            ]);
    
            if($validator->fails()){
                throw new Exception($validator->errors());
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
    
            $user->assignRole($admin);

            if(!$user->hasRole($admin))
            {
                throw new Exception('Failed! Admin creation failed');
            }
            
            DB::commit();
            $this->info('Admin created successfully');

        } catch (\Throwable $th) {

            DB::rollBack();
            $this->error($th->getMessage());
        }
    }
}
