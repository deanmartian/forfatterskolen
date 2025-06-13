<?php

namespace App\Http\Controllers\Backend;

use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\UserTask;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Storage of the model class
     *
     * @var UserTask
     */
    protected $model;

    /**
     * TaskController constructor.
     */
    public function __construct(UserTask $userTask)
    {
        $this->model = $userTask;
    }

    /**
     * Create new task
     */
    public function store(Request $request): RedirectResponse
    {
        $this->model->create($request->except('_token'));

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag('Task created successfully.'),
                'alert_type' => 'success',
                'not-former-courses' => true]);
    }

    /**
     * Finish the task
     */
    public function finishTask($task_id): RedirectResponse
    {
        $task = $this->model->find($task_id);
        if (! $task) {
            return redirect()->back();
        }

        $task->update(['status' => 1]);

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag('Task finished successfully.'),
                'alert_type' => 'success',
                'not-former-courses' => true]);
    }

    /**
     * Update a task
     */
    public function update($task_id, Request $request): RedirectResponse
    {
        $task = $this->model->find($task_id);
        if (! $task) {
            return redirect()->back();
        }

        $task->update($request->except('_token'));

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag('Task updated successfully.'),
                'alert_type' => 'success',
                'not-former-courses' => true]);
    }

    /**
     * Delete a task
     */
    public function destroy($task_id): RedirectResponse
    {
        $task = $this->model->find($task_id);
        if (! $task) {
            return redirect()->back();
        }

        $task->delete();

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag('Task deleted successfully.'),
                'alert_type' => 'success',
                'not-former-courses' => true]);
    }
}
