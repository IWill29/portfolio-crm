<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    /** @use HasFactory<\Database\Factories\ClientFactory> */
    use HasFactory, SoftDeletes;

    /**
     * Drošības slānis: definējam laukus, kurus atļauts aizpildīt masveidā.
     * Tas pasargā no neautorizētām datu izmaiņām (Mass Assignment).
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'notes',
        'status',
    ];

    /**
     * Relācija: Vienam klientam var būt piesaistīti daudzi projekti.
     * Šī "One-to-Many" saite ir būtiska, lai Filamentā parādītu klienta vēsturi.
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}
