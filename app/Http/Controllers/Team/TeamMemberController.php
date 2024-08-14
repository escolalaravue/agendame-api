<?php

namespace App\Http\Controllers\Team;

use App\Exceptions\IsNotATeamMemberException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Team\TeamMemberUpdateRequest;
use App\Http\Resources\TeamMemberResource;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class TeamMemberController extends Controller
{
    public function index()
    {
        $team = Team::find(getPermissionsTeamId());

        return TeamMemberResource::collection($team->users);
    }

    public function update(User $user, TeamMemberUpdateRequest $request)
    {
        $input = $request->validated();

        $team = Team::find(getPermissionsTeamId());
        $isMember = $team->whereHas('users', function($query) use ($user) {
            $query->whereId($user->id);
        })->exists();

        if (!$isMember) {
            throw new IsNotATeamMemberException();
        }

        $user->syncRoles($input['role_id']);
    }

    public function kick(User $user)
    {
        $team = Team::find(getPermissionsTeamId());
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
