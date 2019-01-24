<?php
namespace App\Repositories\Services;

use App\Competition;
use App\Http\Requests\AddCompetitionRequest;
use Carbon\Carbon;

class CompetitionService {

    /**
     * Variable to store the model
     * @var Competition
     */
    protected $competition;

    /**
     * CompetitionService constructor.
     * @param Competition $competition
     */
    public function __construct(Competition $competition)
    {
        $this->competition = $competition;
    }

    /**
     * Get single record or paginated records
     * @param null $id
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function getRecord($id = null)
    {
        if (!is_null($id)) {
            return $this->competition->find($id);
        }
        return $this->competition->all();
    }

    /**
     * Get records that the start date is present
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getActiveRecords()
    {
        return $this->competition->where('start_date','>', Carbon::now())->orderBy('start_date', 'ASC')->get();
    }

    /**
     * Insert the data passed
     * @param AddCompetitionRequest $request
     */
    public function store($request)
    {
        $storeRequest = $request->all();
        if ($request->hasFile('image')) :
            $destinationPath = 'storage/competitions/'; // upload path
            $extension = $request->image->extension(); // getting image extension
            $fileName = time().'.'.$extension; // renaming image
            $request->image->move($destinationPath, $fileName);
            // optimize image
            if ( strtolower( $extension ) == "png" ) :
                $image = imagecreatefrompng($destinationPath.$fileName);
                imagepng($image, $destinationPath.$fileName, 9);
            else :
                $image = imagecreatefromjpeg($destinationPath.$fileName);
                imagejpeg($image, $destinationPath.$fileName, 70);
            endif;
            $storeRequest['image'] = '/'.$destinationPath.$fileName;
        endif;

        $this->competition->create($storeRequest);
    }

    /**
     * Update the competition
     * @param int $id competition id
     * @param AddCompetitionRequest $request
     * @return bool
     */
    public function update($id, $request)
    {
        $competition = $this->getRecord($id);
        if ($competition) {
            $updateCompetition = $request->all();
            if ($request->hasFile('image')) :
                $destinationPath = 'storage/competitions/'; // upload path
                $extension = $request->image->extension(); // getting image extension
                $fileName = time().'.'.$extension; // renaming image
                $request->image->move($destinationPath, $fileName);
                // optimize image
                if ( strtolower( $extension ) == "png" ) :
                    $image = imagecreatefrompng($destinationPath.$fileName);
                    imagepng($image, $destinationPath.$fileName, 9);
                else :
                    $image = imagecreatefromjpeg($destinationPath.$fileName);
                    imagejpeg($image, $destinationPath.$fileName, 70);
                endif;
                $updateCompetition['image'] = '/'.$destinationPath.$fileName;
            endif;
            $competition->update($updateCompetition);
        }
        return false;
    }

    /**
     * Delete a competition
     * @param $id
     * @return bool
     */
    public function destroy($id)
    {
        $competition = $this->getRecord($id);
        if ($competition) {
            $filePath = str_replace('public ','public', public_path().$competition->image);
            if (file_exists($filePath)) {
                unlink($filePath); // delete the physical file
            }
            $competition->forceDelete();
        }
        return false;
    }
    
}