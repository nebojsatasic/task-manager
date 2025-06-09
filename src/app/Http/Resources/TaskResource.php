<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'project_title' => $this->project ? $this->project->title : null,
            'assigned_to' => $this->assignee ? new UserResource($this->assignee) : null,
            'status' => $this->is_done ? 'finished' : 'open',
            'creation-date' => $this->created_at->toDateString(),
        ];
    }
}
