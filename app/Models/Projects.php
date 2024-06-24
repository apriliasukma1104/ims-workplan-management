<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SebastianBergmann\CodeCoverage\Report\Xml\Project;

class Projects extends Model
{
    use HasFactory;
    
    protected $table = 'projects';
    protected $primaryKey = 'id';

    protected $guarded = [];

    public function teamLeader()
    {
        return $this->belongsTo(Members::class, 'team_leader');
    }

    public function teamMembers()
    {
        return $this->belongsToMany(Members::class, 'projects_has_members', 'id_projects', 'id_members');
    }

    public function historyAsIdProjects()
    {
        return $this->hasMany(History::class, 'id_projects');
    }

    public function task_plans()
    {
        return $this->hasMany(TaskPlans::class, 'id_project');
    }

    public function weight_task_plans()
    {
        return $this->hasMany(WeightTaskPlans::class, 'id_project');
    }

    public function task_realizations()
    {
        return $this->hasMany(TaskRealizations::class, 'id_project');
    }
}
