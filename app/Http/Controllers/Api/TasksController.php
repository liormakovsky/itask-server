<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use Validator;

class TasksController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return $this->sendError('User not found', [], 404);
        }
    
        // Check if the authenticated user is an admin
        $isAdmin = $user->role === 'admin';


        if ($isAdmin) {
            // If the user is an admin, retrieve all tasks
            $tasks = Task::with('user')->get();
        } else {
            // If the user is not an admin, retrieve tasks for the specified user
            $tasks = Task::where('user_id', $userId)->with('user')->get();
        }
    
        return $this->sendResponse($tasks, 'Tasks retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        // Get the user ID from the request data
        $userId = $request->input('user_id');

        // Create a new task with the provided data
        $task = Task::create([
            'user_id' => $userId,
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'due_date' => $request->input('due_date'),
        ]);

        return $this->sendResponse($task, 'Task created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $task = Task::find($id);

        if (is_null($task)) {
            return $this->sendError('Task not found');
        }

        return $this->sendResponse($task, 'Task retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'due_date' => 'required|date',
        ]);
    
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }
    
        $task = Task::with('user')->find($id);
    
        if (is_null($task)) {
            return $this->sendError('Task not found');
        }
    
        $task->update($request->all());
    
        // Refresh the task to get the updated user information
        $task->refresh();
    
        return $this->sendResponse($task, 'Task updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $task = Task::find($id);

        if (is_null($task)) {
            return $this->sendError('Task not found');
        }

        $task->delete();

        return $this->sendResponse([], 'Task deleted successfully');
    }
}
