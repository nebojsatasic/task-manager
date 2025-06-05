<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectCollection;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Models\User;
use Spatie\QueryBuilder\QueryBuilder;

class ProjectController extends Controller
{
    /**
     * ProjectController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(Project::class, 'project');
    }

    /**
     * Display a listing of the resource.
     *
     * @return ProjectCollection
     */
    public function index()
    {
        $projects = QueryBuilder::for(Project::class)
            ->allowedIncludes(['tasks', 'members'])
            ->paginate();

        return new ProjectCollection($projects);
    }

    /**
     * Display the specified resource.
     *
     * @input Project $project
     * @return ProjectResource
     */
    public function show(Project $project)
    {
        return new ProjectResource($project->load('tasks')->load('members'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @input StoreProjectRequest $request
     * @return ProjectResource
     */
    public function store(StoreProjectRequest $request)
    {
        $validatedData = $request->validated();

        $project = auth()->user()->projects()->create($validatedData);

        return new ProjectResource($project);
    }

    /**
     * Update the specified resource in storage.
     *
     * @input UpdateProjectRequest $request
     * @input Project $project
     * @return ProjectResource
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $validatedData = $request->validated();

        $project->update($validatedData);

        return new ProjectResource($project);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @input Project $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return response()->noContent();
    }

    public function test()
    {
        $projects = QueryBuilder::for(Project::class)
            ->allowedIncludes(['tasks', 'members'])
            ->paginate();

        return new ProjectCollection($projects);

    }

    /**
     * Attach a user as a member to the given project.
     *
     * If the user is already a member, no changes will be made.
     * Only the creator of the project can attach members (enforced in the "booted" method of the Project model).
     *
     * @input Project $project
     * @input User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function attachMember(Project $project, User $user)
    {
        $this->authorize('attach', $project);

        $project->members()->syncWithoutDetaching([$user->id]);

        return response()->json(['message' => 'Member attached successfully.']);
    }

    /**
     * Detach a user from the given project.
     *
     * If the user is not currently a member, no changes will be made.
     * Only the creator of the project can detach members (enforced in the "booted" method of the Project model).
     * If the user is not a member of the project, no action will be performed.
     *
     * @input Project $project
     * @input User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function detachMember(Project $project, User $user)
    {
        $this->authorize('detach', $project);

        $project->members()->detach($user->id);

        return response()->json(['message' => 'Member detached successfully.']);
    }
}
