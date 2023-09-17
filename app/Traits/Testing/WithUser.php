<?php
namespace App\Traits\Testing; 
use App\Models\User;

trait WithUser {

    /**
     * The user instance.
     *
     * @var \App\Models\User
     */
    protected $user;

    /**
     * Setup up a new user instance.
     *
     * @return \App\Models\User
     */
    protected function setUpUser(): void
    {
        $this->user = User::factory()->create();
    }

    /**
     * @return \App\Models\User
     */
    protected function makeUser($user_data = null): User
    {
        return is_array($user_data) ? User::factory()->make($user_data) : User::factory()->make() ;   
    }

     /**
     * Get the user instance for a given data.
     *
     * @param  array<string ,*>|null  $user_data
     * 
     * @return \App\Models\User
     */
    public function user($user_data = null ): User
    {
        $user = is_array($user_data) ? User::firstOrCreate(User::factory()->make($user_data)->toArray()) : User::first();
        return $user ?? User::factory()->create();
    }

    /**
     * Get a trashed user data.
     *
     * @return \App\Models\User
     */
    public function userTrashed(): User 
    {
        $user_trashed = User::onlyTrashed()->get()->first();
        if($user_trashed)
            return  $user_trashed;
            
        $user_trashed = $this->user();
        $user_trashed->delete();
        return $user_trashed;
    }

}