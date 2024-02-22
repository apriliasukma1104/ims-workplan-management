<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeightTaskPlans extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'id_project', 'id_task_plan', 'month_plan', 'weight_plan'];

    public function project()
    {
        return $this->belongsTo(Projects::class, 'id_project');
    }

    public function task_plans()
    {
        return $this->belongsTo(TaskPlans::class, 'id_task_plan');
    }
}
