<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TerritoryRequest extends Model
{
    protected $fillable = [
        'user_id',
        'territory_id',
        'expected_return_date',
        'status',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function territory()
    {
        return $this->belongsTo(Territory::class);
    }
}
