<?php

namespace App\Http\Controllers\Team;

use App\Exceptions\IsNotATeamMemberException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Team\TeamMemberUpdateRequest;
use App\Http\Resources\TeamMemberResource;
use App\Models\Team;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\Permission\Models\Role;

class TeamMemberController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index(): AnonymousResourceCollection
    {
        $team = app('currentTeam');
        $this->authorize('memberIndex', $team);

        return TeamMemberResource::collection($team->users);
    }

    /**
     * @param User $user
     * @param TeamMemberUpdateRequest $request
     * @return void
     * @throws IsNotATeamMemberException
     * @throws AuthorizationException
     */
    public function update(User $user, TeamMemberUpdateRequest $request): void
    {
        $input = $request->validated();

        $team = app('currentTeam');
        $this->authorize('memberUpdate', $team);

        $isMember = $team->whereHas('users', function($query) use ($user) {
            $query->whereId($user->id);
        })->exists();

        if (!$isMember) {
            throw new IsNotATeamMemberException();
        }

        $user->syncRoles($input['role']);
    }

    /**
     * @param User $user
     * @return void
     * @throws AuthorizationException
     * @throws IsNotATeamMemberException
     */
    public function kick(User $user): void
    {
        $team = app('currentTeam');
        $this->authorize('memberKick', $team);

        $isMember = $team->whereHas('users', function($query) use ($user) {
            $query->whereId($user->id);
        })->exists();

        if (!$isMember) {
            throw new IsNotATeamMemberException();
        }

        Role::all()->pluck('id')->each(function($role) use ($user) {
            $user->removeRole($role);
        });
    }

}
