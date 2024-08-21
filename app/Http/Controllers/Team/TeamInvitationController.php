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
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class TeamInvitationController extends Controller
{

    /**
     * @return AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index(): AnonymousResourceCollection
    {
        $team = app('currentTeam');
        $this->authorize('invitationIndex', $team);

        $invitations = $team->invitations;
        return TeamInvitationResource::collection($invitations);
    }

    /**
     * @param TeamInvitation $teamInvitation
     * @return TeamInvitationPublicResource
     */
    public function show(TeamInvitation $teamInvitation): TeamInvitationPublicResource
    {
        return new TeamInvitationPublicResource($teamInvitation);
    }

    /**
     * @param TeamInvitation $teamInvitation
     * @return void
     * @throws UserNotFoundException
     */
    public function accept(TeamInvitation $teamInvitation): void
    {
        $user = User::query()->whereEmail($teamInvitation->email)->first();
        if (!$user) {
            throw new UserNotFoundException();
        }

        setPermissionsTeamId($teamInvitation->team_id);
        $user->assignRole($teamInvitation->role->name);

        $teamInvitation->delete();
    }

    /**
     * @param TeamInvitationStoreRequest $request
     * @return TeamInvitationResource
     * @throws AuthorizationException
     * @throws UserHasBeenInvitedException
     */
    public function store(TeamInvitationStoreRequest $request): TeamInvitationResource
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

    /**
     * @param TeamInvitation $teamInvitation
     * @return void
     * @throws AuthorizationException
     */
    public function destroy(TeamInvitation $teamInvitation): void
    {
        $team = app('currentTeam');
        $this->authorize('invitationDestroy', $team);

        $teamInvitation->delete();
    }
}
