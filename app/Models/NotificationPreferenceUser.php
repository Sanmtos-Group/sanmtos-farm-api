<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\Pivot;

class NotificationPreferenceUser extends Pivot
{
    use HasUuids;

}
