<?php

namespace App\Http\Controllers\Team;

use App\Events\UserInvited;
use App\Exceptions\UserHasBeenInvitedException;
use App\Exceptions\UserNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Team\TeamInvitationStoreRequest;
use App\Http\Resources\TeamInvitationPublicResource;
use App\Http\Resources\TeamInvitationResource;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class TeamInvitationController extends Controller
{

    public function index()
    {
        $team = app('currentTeam');
        $this->authorize('invitationIndex', $team);

        $invitations = $team->invitations;
        return TeamInvitationResource::collection($invitations);
    }

    public function show(TeamInvitation $teamInvitation)
    {
        return new TeamInvitationPublicResource($teamInvitation);
    }

    public function accept(TeamInvitation $teamInvitation)
    {
        $user = User::query()->whereEmail($teamInvitation->email)->first();
        if (!$user) {
            throw new UserNotFoundException();
        }

        setPermissionsTeamId($teamInvitation->team_id);
        $user->assignRole($teamInvitation->role->name);

        $teamInvitation->delete();
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

        $input['role_id'] = Role::query()->whereName($input['role'])->first()->id;
        $invitation = $team->invitations()->create($input);

        UserInvited::dispatch($invitation);

        return new TeamInvitationResource($invitation);
    }

    public function destroy(TeamInvitation $teamInvitation)
    {
        $team = app('currentTeam');
        $this->authorize('invitationDestroy', $team);

        $teamInvitation->delete();
    }
}
