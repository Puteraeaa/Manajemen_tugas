<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    public function project() {
        return $this->belongsTo(Project::class);
    }
    
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function assignedBy()
{
    return $this->belongsTo(User::class, 'assigned_by');
}


    protected $fillable = [
        'name',
        'description',
        'status',
        'priority',
        'deadline',
        'project_id',
        'user_id',
        'estimated_time',
        'attachment_path',
        'start_date',
        'title',
        'assigned_by',
    ];
}
