<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Controller;
use App\Http\Requests\Team\TeamStoreRequest;
use App\Http\Resources\TeamResource;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $user = auth()->user();
        return TeamResource::collection($user->teams);
    }

    /**
     * @param TeamStoreRequest $request
     * @return TeamResource
     */
    public function store(TeamStoreRequest $request): TeamResource
    {
        $input = $request->validated();
        $input['token'] = Str::uuid();

        $team = Team::query()->create($input);

        $user = auth()->user();
        setPermissionsTeamId($team->id);
        $user->assignRole('admin');

        return new TeamResource($team);
    }
}
