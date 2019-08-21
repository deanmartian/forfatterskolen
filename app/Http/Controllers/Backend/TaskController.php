<?php
namespace App\Http\Controllers\Backend;

use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\UserTask;
use Illuminate\Http\Request;

class TaskController extends Controller {

    /**
     * Storage of the model class
     * @var UserTask
     */
    protected $model;

    /**
     * TaskController constructor.
     * @param UserTask $userTask
     */
    public function __construct(UserTask $userTask)
    {
        $this->model = $userTask;
    }

    /**
     * Create new task
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->model->create($request->except('_token'));
        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag('Task created successfully.'),
                'alert_type' => 'success',
                'not-former-courses' => true]);
    }

    /**
     * Finish the task
     * @param $task_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function finishTask($task_id)
    {
        $task = $this->model->find($task_id);
        if (!$task) {
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
     * @param $task_id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($task_id, Request $request)
    {
        $task = $this->model->find($task_id);
        if (!$task) {
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
     * @param $task_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($task_id)
    {
        $task = $this->model->find($task_id);
        if (!$task) {
            return redirect()->back();
        }

        $task->delete();
        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag('Task deleted successfully.'),
                'alert_type' => 'success',
                'not-former-courses' => true]);
    }

}