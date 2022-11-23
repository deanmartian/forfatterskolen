<?php

namespace App\Http\Controllers\Backend;


use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\MarketingPlan;
use App\MarketingPlanQuestion;
use Illuminate\Http\Request;

class MarketingPlanController extends Controller
{

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $marketingPlans = MarketingPlan::with('questions')->get();
        $marketingPlanStoreRoute = 'admin.marketing-plan.store';
        $marketingPlanUpdateRoute = 'admin.marketing-plan.update';
        $marketingPlanDeleteRoute = 'admin.marketing-plan.destroy';
        return view('backend.marketing-plan.index', compact('marketingPlans', 'marketingPlanStoreRoute',
            'marketingPlanUpdateRoute', 'marketingPlanDeleteRoute'));
    }

    /**
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function store( Request $request )
    {
        $this->validate($request, [
            'name' => 'required'
        ]);

        $marketingPlan = MarketingPlan::create([
            'name' => $request->name
        ]);

        $this->saveData($marketingPlan, $request);

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Marketing plan created successfully.'),
            'alert_type' => 'success'
        ]);
    }

    /**
     * @param $id
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function update( $id, Request $request )
    {
        $this->validate($request, [
            'name' => 'required'
        ]);

        $marketingPlan = MarketingPlan::find($id);
        $marketingPlan->update([
            'name' => $request->name
        ]);

        $this->saveData($marketingPlan, $request);

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Marketing plan updated successfully.'),
            'alert_type' => 'success'
        ]);
    }

    /**
     * @param $marketingPlan
     * @param Request $request
     */
    public function saveData( $marketingPlan, Request $request )
    {
        $main_question_ids = array_column($request->arr, 'main_question_id');
        // delete removed questions
        $marketingPlan->questions()->whereNotIn('id', $main_question_ids)->delete();

        foreach ($request->arr as $input) {
            $marketingPlanQuestions = isset($input['main_question_id']) && $input['main_question_id']
                ? MarketingPlanQuestion::find($input['main_question_id']) : new MarketingPlanQuestion();
            $subQuestion = isset($input['sub_question'])
                ? json_encode($input['sub_question'], JSON_UNESCAPED_UNICODE ) : NULL;

            $marketingPlanQuestions->marketing_plan_id = $marketingPlan->id;
            $marketingPlanQuestions->main_question = $input['main_question'];
            $marketingPlanQuestions->sub_question = $subQuestion;

            $marketingPlanQuestions->save();
        }
    }

    /**
     * @param $id
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function destroy( $id )
    {
        $marketingPlan = MarketingPlan::find($id);
        $marketingPlan->delete();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Marketing plan deleted successfully.'),
            'alert_type' => 'success'
        ]);
    }

}