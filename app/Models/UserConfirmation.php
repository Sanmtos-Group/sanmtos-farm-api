<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;

class UserConfirmation extends Model
{
    use HasFactory;

    public $table = "user_confirmations";

    /**
     * Write code on Method
     *
     * @return response()
     */
    protected $fillable = [
        'user_id',
        'token',
    ];

    /**
     * Write code on Method
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo()
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
