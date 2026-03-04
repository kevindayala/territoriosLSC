<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Territory;
use App\Models\User;

class TerritoryAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'territory_id',
        'assigned_to_user_id',
        'assigned_by_user_id',
        'assigned_at',
        'completed_at',
        'type',
        'due_date',
    ];

    protected $casts = [
        'assigned_at' => 'date',
        'completed_at' => 'date',
        'due_date' => 'date',
    ];

    public function territory()
    {
        return $this->belongsTo(Territory::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by_user_id');
    }
}
