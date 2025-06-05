<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskCollection;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Retrieves the authenticated user's data.
     *
     * @input Request $request
     * @return UserResource
     */
    public function user(Request $request)
    {
        $user = $request->user();

        return new UserResource($user);
    }

    /**
     * Get all tasks created by the authenticated user.
     *
     * @return TaskCollection
     */
    public function myTasks()
    {
        $tasks = auth()->user()->tasks;

        return new TaskCollection($tasks);
    }

    /**
     * Get all tasks assigned to the authenticated user.
     *
     * @return TaskCollection
     */
    public function assignedTasks()
    {
        $tasks = auth()->user()->assignedTasks;

        return new TaskCollection($tasks);
    }
}
