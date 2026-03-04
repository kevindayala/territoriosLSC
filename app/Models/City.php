<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['parent_id', 'name', 'slug', 'is_active'];



    public function parent()
    {
        return $this->belongsTo(City::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(City::class, 'parent_id');
    }

    public function getDisplayNameAttribute()
    {
        return $this->parent_id ? '— ' . $this->name : $this->name;
    }

    public function scopeHierarchical($query)
    {
        return $query->leftJoin('cities as parents', 'cities.parent_id', '=', 'parents.id')
            ->orderByRaw('COALESCE(parents.name, cities.name) ASC')
            ->orderByRaw('cities.parent_id IS NOT NULL ASC')
            ->orderBy('cities.name', 'ASC')
            ->select('cities.*');
    }

    public function territories()
    {
        return $this->hasMany(Territory::class);
    }
}
