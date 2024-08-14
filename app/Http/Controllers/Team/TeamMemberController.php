<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Controller;
use App\Http\Resources\TeamMemberResource;
use App\Models\Team;
use Illuminate\Http\Request;

class TeamMemberController extends Controller
{
    public function index()
    {
        $team = Team::find(getPermissionsTeamId());

        return TeamMemberResource::collection($team->users);
    }
}
