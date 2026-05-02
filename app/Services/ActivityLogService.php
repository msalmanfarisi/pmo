<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogService
{
    public static function log(Model $model, string $action, ?string $description = null, ?array $properties = null): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => Auth::id(),
            'loggable_type' => get_class($model),
            'loggable_id' => $model->getKey(),
            'action' => $action,
            'description' => $description,
            'properties' => $properties,
            'ip_address' => Request::ip(),
        ]);
    }
}
