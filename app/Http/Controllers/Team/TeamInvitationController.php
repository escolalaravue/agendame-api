<?php

namespace App\Http\Controllers\Team;

use App\Events\UserInvited;
use App\Exceptions\UserHasBeenInvitedException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Team\TeamInvitationStoreRequest;
use App\Http\Resources\TeamInvitationResource;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TeamInvitationController extends Controller
{

    public function index()
    {
        $team = app('currentTeam');
        $this->authorize('invitationIndex', $team);

        $invitations = $team->invitations;
        return TeamInvitationResource::collection($invitations);

    }
    public function store(TeamInvitationStoreRequest $request)
    {
        $team = app('currentTeam');
        $this->authorize('invitationStore', $team);

        $input = $request->validated();
        $input['token'] = Str::uuid();

        $email = $input['email'];
        $isInvited = $team
            ->whereHas('users', function($query) use ($email) {
                $query->whereEmail($email);
            })
            ->orWhereHas('invitations', function($query) use ($email) {
                $query->whereEmail($email);
            })
            ->exists();

        if ($isInvited) {
            throw new UserHasBeenInvitedException();
        }

        $invitation = $team->invitations()->create($input);

        UserInvited::dispatch($invitation);

        return new TeamInvitationResource($invitation);
    }
}
