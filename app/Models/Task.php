<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'status', 'assigned_to', 'due_date', 'project_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Relationship to the Project model
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
