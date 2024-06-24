<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskPlans extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function project()
    {
        return $this->belongsTo(Projects::class, 'id_project');
    }

    public function weight_task_plans()
    {
        return $this->hasMany(WeightTaskPlans::class, 'id_task_plans','id');
    }  
    
    public function task_realizations()
    {
        return $this->hasMany(TaskRealizations::class, 'id_task_plans', 'id');
    }   
}
