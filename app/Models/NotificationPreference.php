<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
     * The notifications preferences that belongs to the user (vendor).
     */
    public function subscribers()
    {
        return $this->belongsToMany(User::class)->using(NotificationPreferenceUser::class);

    }
}
