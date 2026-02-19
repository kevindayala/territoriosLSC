<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Territory;
use App\Models\User;

class Person extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'persons'; // Explicit table name just in case

    protected $fillable = [
        'full_name',
        'address',
        'map_url',
        'territory_id',
        'status',
        'inactive_reason_note',
        'created_by_user_id',
        'approved_at',
        'approved_by_user_id',
        'notes',
        'pending_changes',
        'pending_by_user_id',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'pending_changes' => 'array',
    ];

    public function territory()
    {
        return $this->belongsTo(Territory::class);
    }

    public function pendingUser()
    {
        return $this->belongsTo(User::class, 'pending_by_user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }
}
