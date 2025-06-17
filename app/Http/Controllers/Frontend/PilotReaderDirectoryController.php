<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\PilotReaderBook;
use App\PilotReaderBookReading;
use App\PilotReaderReaderProfile;
use App\PilotReaderReaderQuery;
use App\PilotReaderReaderQueryDecision;
use App\Transformer\ReaderQueriesTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class PilotReaderDirectoryController extends Controller
{
    /**
     * Display the reader directory page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(): View
    {
        return view('frontend.learner.pilot-reader.reader-directory.index');
    }

    /**
     * Display the about page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function about(): View
    {
        return view('frontend.learner.pilot-reader.reader-directory.about');
    }

    /**
     * Display query reader sent list
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function queryReaderSentList(): View
    {
        return view('frontend.learner.pilot-reader.reader-directory.sent-query');
    }

    /**
     * Display query reader receive list
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function queryReaderReceivedList(): View
    {
        return view('frontend.learner.pilot-reader.reader-directory.received-query');
    }

    /**
     * List all reader profiles available
     */
    public function listReaderProfile(Request $request): JsonResponse
    {
        $readers = PilotReaderReaderProfile::where([['availability', '=', 1], ['user_id', '<>', Auth::user()->id]]);
        if (count($request->all())) {
            $search = $request->search ?: '';
            $search_col = [
                [['genre_preferences', 'LIKE', '%'.($request->genre_preferences ?: $search).'%']],
                [['dislike_contents', 'LIKE', '%'.($request->dislike_contents ?: $search).'%']],
                [['expertise', 'LIKE', '%'.($request->expertise ?: $search).'%']],
                [['favourite_author', 'LIKE', '%'.($request->favourite_author ?: $search).'%']],
            ];
            $readers->where(function ($query) use ($search_col) {
                foreach ($search_col as $key => $search) {
                    if ($search[0][2] === '%%') {
                        continue;
                    }
                    $query->orWhere($search);
                }
            });
        }

        return response()->json($readers->with('user')->paginate(5));
    }

    /**
     * List the book that the reader is not reading yet
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|static[]
     */
    public function listBook(Request $request)
    {
        return PilotReaderBook::whereNotIn('id', function ($query) use ($request) {
            $query->select('book_id')
                ->from('pilot_reader_book_reading')
                ->where('user_id', $request->author_id);
        })->whereNotIn('id', function ($query) use ($request) {
            $query->select('book_id')
                ->from('pilot_reader_reader_queries')
                ->where('to', $request->author_id)
                ->where('status', '<>', 2);
        })->get();

    }

    /**
     * Add a query to reader
     */
    public function queryReader(Request $request): JsonResponse
    {
        $request->validate([
            'book_id' => 'required',
        ], [
            'book_id.required' => 'Please select a book first.',
        ]);

        $data = $request->all();
        $data['from'] = Auth::user()->id;
        if (! PilotReaderReaderQuery::create($data)) {
            return response()->json(['error' => 'Opss. Something went wrong'], 500);
        }

        return response()->json(['success' => 'Reader Queried'], 200);
    }

    /**
     * List the queries
     */
    public function listQueries(Request $request): JsonResponse
    {
        $fractal = new Manager;
        $col = $request->list === 'sent' ? 'from' : 'to';
        $query = PilotReaderReaderQuery::where([$col => Auth::user()->id])->get();
        $resource = new Collection($query, new ReaderQueriesTransformer); // transform the data to be passed
        $queries = $fractal->createData($resource)->toArray();

        return response()->json(compact('queries'));
    }

    /**
     * Save the decision of the logged in user
     */
    public function saveQueryDecision(Request $request): JsonResponse
    {
        $decision_data = $request->except('want_to_read', 'book_id');
        \DB::beginTransaction();
        if (! PilotReaderReaderQuery::find($request->query_id)->update(['status' => $request->want_to_read])) {
            \DB::rollback();

            return response()->json(['error' => 'Opss. Something went wrong'], 500);
        }
        if (! PilotReaderReaderQueryDecision::create($decision_data)) {
            \DB::rollback();

            return response()->json(['error' => 'Opss. Something went wrong'], 500);
        }
        // if the user selects want to read option then insert him/her as a new reader of the book
        if ($request->want_to_read == 1) {
            $reader_data = $request->only('book_id');
            $reader_data['user_id'] = Auth::user()->id;
            if (! PilotReaderBookReading::create($reader_data)) {
                \DB::rollback();

                return response()->json(['error' => 'Opss. Something went wrong'], 500);
            }
        }
        \DB::commit();

        return response()->json(['success' => 'Query Decision Submitted'], 200);
    }
}
