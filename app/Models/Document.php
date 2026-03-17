<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Models\Traits\LogsActivity;

class Document extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\DocumentFactory> */
    use HasFactory, SoftDeletes, InteractsWithMedia, LogsActivity;

    protected $fillable = [
        'project_id',
        'title',
        'type',
        'notes',
    ];

    /**
     * Relācija: Dokuments vienmēr ir piesaistīts konkrētam projektam.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
    
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments')
             ->useDisk('public');
    }
}
