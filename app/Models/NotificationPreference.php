<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class NotificationPreference extends Model
{
    use HasFactory;
    use HasUuids;

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'description',
        'channel',
        'type',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'user_is_a_subcriber',
    ];
    

    /**
     * The notifications preferences that belongs to the user (vendor).
     */
    public function subscribers()
    {
        return $this->belongsToMany(User::class)->using(NotificationPreferenceUser::class);

    }

    /**
     * Determine if current auth user is a subscriber
     */
    protected function userIsASubcriber(): Attribute
    {
        $user = auth()->user();
        return Attribute::make(
            get: fn () => is_null($user)? false : !is_null($this->subscribers()->where('user_id', $user->id)->first())
        );
    }
}
