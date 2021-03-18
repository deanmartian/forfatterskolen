<?php
namespace App\Http\Controllers\Backend;

use App\Editor;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\EditorCreateRequest;
use App\Http\Requests\EditorUpdateRequest;
use Illuminate\Http\Request;
use File;
use Illuminate\Support\Facades\DB;
use App\User;
use App\EditorAssignmentPrices;

class EditorController extends Controller
{
    /**
     * Display all editors
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $editors = Editor::paginate(15);
        return view('backend.editor.index', compact('editors'));
    }

    /**
     * Display the create page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $editor = [
            'name' => old('name'),
            'description' => old('description'),
            'editor_image' => ''
        ];
        return view('backend.editor.create', compact('editor'));
    }

    /**
     * Create new editor
     * @param EditorCreateRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(EditorCreateRequest $request)
    {
        $editor                 = new Editor();
        $editor->name           = $request->name;
        $editor->description    = $request->description;

        if ($request->hasFile('editor_image')) :
            $destinationPath = 'images/editors'; // upload path
            $extension = $request->editor_image->extension(); // getting image extension
            $uploadedFile = $request->editor_image->getClientOriginalName();
            $actual_name = pathinfo($uploadedFile, PATHINFO_FILENAME);
            $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension);// rename document
            $request->editor_image->move($destinationPath, $fileName);

            // optimize image
            if ( strtolower( $extension ) == "png" ) :
                $image = imagecreatefrompng($fileName);
                imagepng($image, $fileName, 9);
            else :
                $image = imagecreatefromjpeg($fileName);
                imagejpeg($image, $fileName, 70);
            endif;
            $editor->editor_image = '/'.$fileName;
        endif;

        $editor->save();

       return redirect('/editor');
    }

    /**
     * Display edit page
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $editor = Editor::findOrFail($id)->toArray();
        return view('backend.editor.edit', compact('editor'));
    }

    /**
     * Update the editor
     * @param $id
     * @param EditorUpdateRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update($id, EditorUpdateRequest $request)
    {
        $editor = Editor::find($id);
        if ($editor) {
            $editor->name           = $request->name;
            $editor->description    = $request->description;

            if ($request->hasFile('editor_image')) :
                $destinationPath = 'images/editors'; // upload path
                $extension = $request->editor_image->extension(); // getting image extension
                $uploadedFile = $request->editor_image->getClientOriginalName();
                $actual_name = pathinfo($uploadedFile, PATHINFO_FILENAME);
                $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension);// rename document
                $request->editor_image->move($destinationPath, $fileName);

                // optimize image
                if ( strtolower( $extension ) == "png" ) :
                    $image = imagecreatefrompng($fileName);
                    imagepng($image, $fileName, 9);
                else :
                    $image = imagecreatefromjpeg($fileName);
                    imagejpeg($image, $fileName, 70);
                endif;
                $editor->editor_image = '/'.$fileName;
            endif;

            $editor->save();
        }
        return redirect('/editor');
    }

    /**
     * Delete the editor
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id){
        $editor = Editor::find($id);
        if ($editor) {
            $image = substr($editor->editor_image, 1);
            if( File::exists($image) ) :
                File::delete($image);
            endif;
            $editor->forceDelete();
        }
        return redirect('/editor');
    }

    public function total($editor_id){
        $pAssgn = DB::select("call editor_total_worked_personal_assignment($editor_id, null, null)");
        $shpMan = DB::select("call editor_total_worked_shop_manuscript($editor_id, null, null)");
        $gAssgn = DB::select("call editor_total_worked_group_assignment($editor_id, null, null)");
        $chngTmr = DB::select("call editor_total_worked_coaching($editor_id, null, null)");
        $crrctn = DB::select("call editor_total_worked_correction($editor_id, null, null)");
        $cpyEdtng = DB::select("call editor_total_worked_copy_editing($editor_id, null, null)");
        $all = array_merge($pAssgn, $shpMan, $gAssgn, $chngTmr, $crrctn, $cpyEdtng);

        if(!$all){
            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('No data found.'),
                'alert_type' => 'warning'
            ]);
        }

        $year_month = 'year_month';

        $maxYearMonth = max(array_map(function($o) use($year_month) {
            return $o->$year_month;
            },
            $all));

        $minYearMonth = min(array_map(function($o) use($year_month) {
        return $o->$year_month;
        },
        $all));

        $minYear = substr($minYearMonth,0,4);
        $minMonth = substr($minYearMonth,-2);
        $maxYear = substr($maxYearMonth,0,4);
        $maxMonth = substr($maxYearMonth,-2);

        $var = [
            'minYear' =>  $minYear,
            'minMonth' =>  $minMonth,
            'maxYear' =>  $maxYear,
            'maxMonth' =>  $maxMonth
        ];

        $data = [
            'pAssgn' => $pAssgn,
            'shpMan' => $shpMan,
            'gAssgn' => $gAssgn,
            'chngTmr' => $chngTmr,
            'crrctn' => $crrctn,
            'cpyEdtng' => $cpyEdtng
        ];

        $editor = User::find($editor_id)->FullName;
        $prices = EditorAssignmentPrices::all();

        $assgnPrice = 0;
        $shpManPrice = 0;
        $chngTmrPrice = 0;
        $crrctnPrice = 0;
        $cpyEdtngPrice = 0;
        foreach ($prices as $key) {
            if ($key->assignment == 'Assignment'){
                $assgnPrice = $key->price;
            }elseif($key->assignment == 'Shop Manuscript'){
                $shpManPrice = $key->price;
            }elseif($key->assignment == 'Coaching Timer'){
                $chngTmrPrice = $key->price;
            }elseif($key->assignment == 'Correction'){
                $crrctnPrice = $key->price;
            }elseif($key->assignment == 'Copy Editing'){
                $cpyEdtngPrice = $key->price;
            }
        }

        $price = [
            'assgnPrice' => $assgnPrice,
            'shpManPrice' => $shpManPrice,
            'chngTmrPrice' => $chngTmrPrice,
            'crrctnPrice' => $crrctnPrice,
            'cpyEdtngPrice' => $cpyEdtngPrice
        ];

        return view('backend.admin.total_editor_worked', compact('editor', 'var', 'data', 'editor','price'));
    }
}