<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
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
        'html_input_type',
        'select_options',
        'label',
        'description',
        'key',
        'value',
        'group_name',
        'settable_id',
        'settable_type',
        'allowed_editor_roles',
        'allowed_view_roles',
        'owner_feature',
    ];

     /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'select_options' => 'json',
        'allowed_editor_roles' => 'json',
        'allowed_view_roles' => 'json',
    ];
}
