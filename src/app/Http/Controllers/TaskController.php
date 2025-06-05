<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskCollection;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class TaskController extends Controller
{
    /**
     * TaskController Constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(Task::class, 'task');
    }

    /**
     * Display a listing of the resource.
     *
     * @return TaskCollection
     */
    public function index(Request $request)
    {
        $is_done = $request->input('filter.is_done');

        if ($is_done == 'false' || $is_done == '0') {
            $request->merge([
                'filter' => [
                    'is_done' => 0,
                ],
            ]);
        }

        $tasks = QueryBuilder::for(Task::class)
            ->allowedFilters('is_done')
            ->defaultSort('-created_at')
            ->allowedSorts(['title', 'is_done', 'created_at'])
            ->paginate();

        return new TaskCollection($tasks);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @input StoreTaskRequest $request
     * @return TaskResource
     */
    public function store(StoreTaskRequest $request)
    {
        $validatedData = $request->validated();

        $task = auth()->user()->tasks()->create($validatedData);

        return new TaskResource($task);
    }

    /**
     * Display the specified resource.
     *
     * @input Task $task
     * @return TaskResource
     */
    public function show(Task $task)
    {
        return new TaskResource($task);
    }

    /**
     * Update the specified resource in storage.
     *
     * @input UpdateTaskRequest $request
     * @input Task $task
     * @return TaskResource
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $validatedData = $request->validated();

        $task->update($validatedData);

        return new TaskResource($task);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @input Task $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return response()->noContent();
    }

    /**
     * Assign a member to the given task.
     *
     * This method checks whether the authenticated user is authorized to assign a user
     * to the task — specifically, whether they are the creator of the task. It also verifies
     * that the target user is a member of the project the task belongs to. If both checks pass,
     * the task's assignee is updated.
     *
     * @input Task $task
     * @input User $user
     * @return \Illuminate\Http\JsonResponse|TaskResource
     */
    public function assign(Task $task, User $user)
    {
        $this->authorize('assign', $task);

        $project = $task->project;
        $memberIds = $project->members->pluck('id');

        if (! $memberIds->contains($user->id)) {
            return response()->json([
                'message' => 'You cannot assign this user to this task.',
            ], 403);
        }

        $task->update(['assignee_id' => $user->id]);

        return new TaskResource($task);
    }

    /**
     * Unassign a member from the given task.
     *
     * This method checks whether the authenticated user is authorized to unassign a member
     * from the task — specifically, whether they are the creator of the task. If the check passes,
     * the task's assignee is cleared.
     *
     * @input Task $task
     * @return \Illuminate\Http\JsonResponse|TaskResource
     */
    public function unassign(Task $task)
    {
        $this->authorize('unassign', $task);

        $task->update(['assignee_id' => null]);

        return new TaskResource($task);
    }
}
