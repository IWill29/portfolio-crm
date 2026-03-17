<?php

namespace App\Models\Traits;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity as SpatieLogsActivity;

trait LogsActivity
{
    use SpatieLogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(class_basename($this))
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->logOnly([
                'title',
                'name',
                'status',
                'budget',
                'notes',    
                'company',
            ]);
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        $label = $this->title ?? $this->name ?? $this->id;

        return sprintf('%s "%s" was %s', class_basename($this), $label, $eventName);
    }
}