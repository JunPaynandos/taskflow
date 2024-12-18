<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    // public function user()  
    // {
    //     return $this->belongsTo(User::class);
    // }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'project_user');
    }

    public function owner()
    {
        return $this->belongsTo(User::class);  // Assuming the owner is a User model
    }

    public function members()
    {
        return $this->belongsToMany(User::class);  // Assuming the project has many members
    }
}
