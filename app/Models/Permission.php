<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * The roles that has the permission.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, PermissionRole::class);
    }
}
