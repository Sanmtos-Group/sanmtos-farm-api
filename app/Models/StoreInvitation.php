<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreInvitation extends Model
{
    use HasFactory;
    use HasUuids;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'store_id',
        'email',
        'roles',
        'expires_at',
        'status',
        'accepted_at',
        'declined_at',
    ];


    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => \App\Events\StoreInvitation\StoreInvitationCreated::class,
    ];



    /**
     * Is accepted attribute
     * 
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function isAccepted(): Attribute
    {
        return Attribute::make(
            get: fn () => !is_null($this->declined_at) ? false: (is_null($this->accepted_at)? null: true),
        );
    }

     /**
     * Get the store of the store invitation .
     * 
     * @return \App\Models\Store || null
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    /**
     * Get the invited user of the store invitation.
     * 
     * @return \App\Models\User || null
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }
  
}
