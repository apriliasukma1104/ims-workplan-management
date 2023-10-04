<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Productivity extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'id_project', 'id_task', 'id_member', 'comment', 'subject', 'date', 'start_time', 'end_time'];
}
