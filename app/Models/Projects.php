<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SebastianBergmann\CodeCoverage\Report\Xml\Project;

class Projects extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'project_type',
        'team_leader',
        'team_members',
        'start_date',
        'end_date',
        'status',
        'description',
        'validation',
        'note',
        
    ];

    public function teamLeader()
    {
        return $this->belongsTo(Members::class, 'team_leader');
    }

    public function teamMembers()
    {
        return $this->belongsToMany(Members::class, 'project_team_members', 'project_id', 'member_id');
    }

    public function tasks()
    {
        return $this->hasMany(Tasks::class, 'id_project');
    }

}
