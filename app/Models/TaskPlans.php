<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskPlans extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'id_project', 'type', 'task', 'description', 'start_month', 'end_month', 'tot_weight_plan'];

    public function project()
    {
        return $this->belongsTo(Projects::class, 'id_project');
    }

    public function task_realizations()
    {
        return $this->hasMany(TaskRealizations::class, 'id_task_plan');
    }    

    public function weight_task_plans()
    {
        return $this->hasMany(WeightTaskPlans::class, 'id_task_plan');
    }    
}
