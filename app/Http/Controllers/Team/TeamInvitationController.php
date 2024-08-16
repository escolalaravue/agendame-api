<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Controller;
use App\Http\Requests\Team\TeamInvitationStoreRequest;
use App\Http\Resources\TeamInvitationResource;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TeamInvitationController extends Controller
{
    public function store(TeamInvitationStoreRequest $request)
    {
        $input = $request->validated();
        $input['token'] = Str::uuid();
        $team = app('currentTeam');

        $invitation = $team->invitations()->create($input);

        return new TeamInvitationResource($invitation);
    }
}
