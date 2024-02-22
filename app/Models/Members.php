<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Members extends Authenticatable
{
    protected $table = 'members';
    protected $primaryKey = 'id';
    protected $guarded = [];
    use Notifiable;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        
    ];

    public function projectsAsTeamLeader()
    {
        return $this->hasMany(Projects::class, 'team_leader');
    }

    public function projectsAsTeamMember()
    {
        return $this->belongsToMany(Projects::class, 'projects_has_members', 'id_projects', 'id_members');
    }
}
