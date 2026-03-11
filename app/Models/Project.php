<?php

namespace App\Models;

use App\Enums\ProjectPriority;
use App\Enums\ProjectStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'title',
        'description',
        'budget',
        'status',
        'priority',
        'starts_at',
        'ends_at',
    ];

    /**
     * ✅ Šeit notiek "maģija" — Casts.
     * Mēs pasakām Laravel, ka 'status' un 'priority' nav vienkārši teksti, 
     * bet gan tavi specifiskie PHP Enums.
     */
    protected $casts = [
        'status' => ProjectStatus::class,
        'priority' => ProjectPriority::class,
        'budget' => 'decimal:2',
        'starts_at' => 'date',
        'ends_at' => 'date',
    ];

    /**
     * Relācija: Projekts vienmēr pieder kādam klientam (Many-to-One).
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function documents(): HasMany
    {
     return $this->hasMany(Document::class);
    }
}
