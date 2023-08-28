<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Image extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => \App\Events\Image\ImageCreated::class,
        'deleted' => \App\Events\Image\ImageDeleted::class,
        'restored' => \App\Events\Image\ImageRestored::class,
        'updated' => \App\Events\Image\ImageUpdated::class,
        'trashed' => \App\Events\Image\ImageTrashed::class,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'url',
        'imageable_id',
        'imageable_type',
    ];

}
