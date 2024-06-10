<?php

namespace App\Console\Commands;

use App\Actions\Fortify\PasswordValidationRules;
use App\Models\Role;
use App\Models\User;
use App\Models\Store;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class CreateStoreAdmin extends Command
{
    use PasswordValidationRules;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-store-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a store admin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {

            DB::beginTransaction();
            
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
            $user = new User();

            $user->forceFill([
                'first_name' =>  $input['first_name'],
                'last_name' => $input['last_name'],
                'email' => $input['email'],
                'password' => $input['password'],
            ]);
            $user->email_verified_at = now();
            $user->save();

             // create a store admin role if not exist
             $store_admin = Role::firstOrCreate([
                'name' => 'store-admin',
                'store_id'=> null
            ]);
    
            $user->assignRole($store_admin);


            if(!$user->hasRole($store_admin))
            {
                throw new Exception('Failed! Store admin creation failed');
            }

            $store =  $user->store;

            if(is_null($store))
            {
                $store_name_is_taken = false;
                do {
                    $store_name =  $this->ask('Enter Store Name');
                    $store_name_is_taken = is_null(Store::where('name', $store_name)->first())? false : true;

                    if($store_name_is_taken)
                    {
                        $this->error('The store name is taken..!!');
                        $this->warn('Please try again. Enter 0 to terminate the whole process');
                    }

                    
                } while ($store_name != 0 && $store_name_is_taken) ;

                if(!$store_name_is_taken && $store_name !=0)
                {
                    $store = Store::factory()
                    ->hasImages(1)
                    ->create([
                        'name' => $store_name,
                        'user_id' => $user->id,
                        'description' => 'Welcome to '.ucwords($store_name),
                    ]);       
                }
                else 
                {
                    throw new Exception('Failed! Store admin creation terminated');
                }
                   
            }

            DB::commit();
            $this->info('Store admin created successfully');

        } catch (\Throwable $th) {

            DB::rollBack();
            $this->error($th->getMessage());
        }
    }
}
