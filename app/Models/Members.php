<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use SebastianBergmann\CodeCoverage\Report\Xml\Project;

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
        return $this->belongsToMany(Projects::class, 'project_team_members', 'member_id', 'project_id');
    }
}
