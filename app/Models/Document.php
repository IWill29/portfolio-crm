<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Document extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\DocumentFactory> */
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'project_id',
        'title',
        'type', // piemēram: līgums, skice, rēķins
        'notes',
    ];

    /**
     * Relācija: Dokuments vienmēr ir piesaistīts konkrētam projektam.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Šeit tu vari definēt specifiskas mediju kolekcijas (neobligāti, bet labi portfolio).
     * Tas ļauj ierobežot failu skaitu vai veidus.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments')
             ->useDisk('public');
    }
}
