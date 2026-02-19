<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\City;
use App\Models\Person;
use App\Models\TerritoryAssignment;

class Territory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'city_id',
        'neighborhood_name',
        'status',
        'last_completed_at',
        'notes',
    ];

    protected $casts = [
        'last_completed_at' => 'date',
    ];

    // Neighborhood relationship removed as it's now a text field

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function persons()
    {
        return $this->hasMany(Person::class);
    }

    public function assignments()
    {
        return $this->hasMany(TerritoryAssignment::class);
    }
}
