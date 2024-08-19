<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;

class TeamPolicy
{
    public function update(User $user, Team $team)
    {
        setPermissionsTeamId($team->id);

        return $user->hasRole('admin');
    }

    public function destroy(User $user, Team $team)
    {
        setPermissionsTeamId($team->id);

        return $user->hasRole('admin');
    }

    public function subscribe(User $user, Team $team)
    {
        setPermissionsTeamId($team->id);

        return $user->hasRole('admin');
    }

    public function memberIndex(User $user, Team $team)
    {
        setPermissionsTeamId($team->id);

        return $user->hasRole('admin');
    }

    public function memberUpdate(User $user, Team $team)
    {
        setPermissionsTeamId($team->id);

        return $user->hasRole('admin');
    }

    public function memberKick(User $user, Team $team)
    {
        setPermissionsTeamId($team->id);

        return $user->hasRole('admin');
    }

    public function invitationIndex(User $user, Team $team)
    {
        setPermissionsTeamId($team->id);

        return $user->hasRole('admin');
    }

    public function invitationStore(User $user, Team $team)
    {
        setPermissionsTeamId($team->id);

        return $user->hasRole('admin');
    }
}
