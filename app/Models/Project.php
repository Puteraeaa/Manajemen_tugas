<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Project extends Model
{
    protected $keyType = 'string'; // UUID adalah string
    public $incrementing = false; // Non-incrementing ID

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid(); // Generate UUID
            }
        });
    }
    

    public function tasks() {
        return $this->hasMany(Task::class);
    }


    protected $fillable = [
        'name',
        'description',
        'user_id',
    ];

    public function users()
{
    return $this->belongsToMany(User::class, 'project_user', 'project_id', 'user_id');
}

public function scopeForUser($query, $userId)
{
    return $query->whereHas('users', function ($q) use ($userId) {
        $q->where('users.id', $userId);
    });
}

    
}
