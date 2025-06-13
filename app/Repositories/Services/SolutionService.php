<?php

namespace App\Repositories\Services;

use App\Http\Requests\SolutionCreateRequest;
use App\Solution;
use File;
use Illuminate\Database\Eloquent\Model;

class SolutionService
{
    /**
     * Store the solution model
     *
     * @var Solution
     */
    protected $solution;

    /**
     * SolutionService constructor.
     */
    public function __construct(Solution $solution)
    {
        $this->solution = $solution;
    }

    /**
     * @param  null  $id
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function getRecord($id = null, int $page = 15)
    {
        if ($id) {
            return $this->solution->find($id);
        }

        return $this->solution->paginate($page);
    }

    /**
     * Create new solution
     */
    public function store(SolutionCreateRequest $request): Model
    {
        $requestData = $request->toArray();

        /*if ($request->hasFile('image')) :
            $fileExt = $request->image->extension(); // getting image extension
            $fileType = $request->image->getMimeType();
            $fileTmp = $request->image->getPathName();
            $fileName = time().'.'.$fileExt; // renaming image

            //check if the folder exists if not then create the folder
            if (!file_exists('storage/solution-images/')) {
                File::makeDirectory('storage/solution-images/', 0775, true);
            }

            $largeImageLoc = 'storage/solution-images/'.$fileName; // upload path
            $thumbImageLoc = 'storage/solution-images/thumb/'.$fileName; // upload path thumb

            if(move_uploaded_file($fileTmp, $largeImageLoc)) {
                //file permission
                chmod($largeImageLoc, 0777);

                //get dimensions of the original image
                list($width_org, $height_org) = getimagesize($largeImageLoc);

                //get image coords
                $x = (int)$request->x;
                $y = (int)$request->y;
                $width = (int)$request->w;
                $height = (int)$request->h;

                //define the final size of the cropped image
                $width_new = $width;
                $height_new = $height;

                $source = '';

                //crop and resize image
                $newImage = imagecreatetruecolor($width_new, $height_new);

                switch ($fileType) {
                    case "image/gif":
                        $source = imagecreatefromgif($largeImageLoc);
                        break;
                    case "image/pjpeg":
                    case "image/jpeg":
                    case "image/jpg":
                        $source = imagecreatefromjpeg($largeImageLoc);
                        break;
                    case "image/png":
                    case "image/x-png":
                        $source = imagecreatefrompng($largeImageLoc);
                        break;
                }

                imagecopyresampled($newImage, $source, 0, 0, $x, $y, $width_new, $height_new, $width, $height);

                //check if the folder exists if not then create the folder
                if (!file_exists('storage/solution-images/thumb/')) {
                    File::makeDirectory('storage/solution-images/thumb/', 0775, true);
                }

                switch ($fileType) {
                    case "image/gif":
                        imagegif($newImage, $thumbImageLoc);
                        break;
                    case "image/pjpeg":
                    case "image/jpeg":
                    case "image/jpg":
                        imagejpeg($newImage, $thumbImageLoc, 90);
                        break;
                    case "image/png":
                    case "image/x-png":
                        imagepng($newImage, $thumbImageLoc);
                        break;
                }
                imagedestroy($newImage);

                //remove large image
                unlink($largeImageLoc);

                $requestData['image'] = '/' . $thumbImageLoc;
            }
        endif;*/

        if ($request->hasFile('image')) {
            $destinationPath = 'storage/solution-images/'; // upload path
            $extension = $request->image->extension(); // getting image extension
            $fileName = time().'.'.$extension; // renameing image
            $request->image->move($destinationPath, $fileName);
            // optimize image
            /*if ( strtolower( $extension ) == "png" ) :
                $image = imagecreatefrompng($destinationPath.$fileName);
                imagepng($image, $destinationPath.$fileName, 9);
            else :
                $image = imagecreatefromjpeg($destinationPath.$fileName);
                imagejpeg($image, $destinationPath.$fileName, 70);
            endif;*/
            $requestData['image'] = '/'.$destinationPath.$fileName;
        }

        if (isset($requestData['is_instruction'])) {
            $requestData['is_instruction'] = 1;
        }

        return $this->solution->create($requestData);
    }

    /**
     * Update a solution
     */
    public function update($id, SolutionCreateRequest $request): bool
    {
        $solution = $this->getRecord($id);
        $requestData = $request->toArray();
        if ($solution) {

            /*if ($request->hasFile('image')) :

                $fileExt = $request->image->extension(); // getting image extension
                $fileType = $request->image->getMimeType();
                $fileSize = $request->image->getClientSize();
                $fileTmp = $request->image->getPathName();
                $fileName = time().'.'.$fileExt; // renaming image

                $largeImageLoc = 'storage/solution-images/'.$fileName; // upload path
                $thumbImageLoc = 'storage/solution-images/thumb/'.$fileName; // upload path thumb

                if(move_uploaded_file($fileTmp, $largeImageLoc)){
                    //file permission
                    chmod ($largeImageLoc, 0777);

                    //get dimensions of the original image
                    list($width_org, $height_org) = getimagesize($largeImageLoc);

                    //get image coords
                    $x = (int) $request->x;
                    $y = (int) $request->y;
                    $width = (int) $request->w;
                    $height = (int) $request->h;

                    //define the final size of the cropped image
                    $width_new = $width;
                    $height_new = $height;

                    $source = '';

                    //crop and resize image
                    $newImage = imagecreatetruecolor($width_new,$height_new);

                    switch($fileType) {
                        case "image/gif":
                            $source = imagecreatefromgif($largeImageLoc);
                            break;
                        case "image/pjpeg":
                        case "image/jpeg":
                        case "image/jpg":
                            $source = imagecreatefromjpeg($largeImageLoc);
                            break;
                        case "image/png":
                        case "image/x-png":
                            $source = imagecreatefrompng($largeImageLoc);
                            break;
                    }

                    imagecopyresampled($newImage,$source,0,0,$x,$y,$width_new,$height_new,$width,$height);

                    //check if the folder exists if not then create the folder
                    if (!file_exists('storage/solution-images/thumb/')) {
                        File::makeDirectory('storage/solution-images/thumb/', 0775, true);
                    }

                    switch($fileType) {
                        case "image/gif":
                            imagegif($newImage,$thumbImageLoc);
                            break;
                        case "image/pjpeg":
                        case "image/jpeg":
                        case "image/jpg":
                            imagejpeg($newImage,$thumbImageLoc,90);
                            break;
                        case "image/png":
                        case "image/x-png":
                            imagepng($newImage,$thumbImageLoc);
                            break;
                    }
                    imagedestroy($newImage);

                    //remove large image
                    unlink($largeImageLoc);

                    $requestData['image'] = '/'.$thumbImageLoc;
                }

            endif;*/

            if ($request->hasFile('image')) {
                $destinationPath = 'storage/solution-images/'; // upload path
                $extension = $request->image->extension(); // getting image extension
                $fileName = time().'.'.$extension; // renameing image
                $request->image->move($destinationPath, $fileName);
                // optimize image
                /*if ( strtolower( $extension ) == "png" ) :
                    $image = imagecreatefrompng($destinationPath.$fileName);
                    imagepng($image, $destinationPath.$fileName, 9);
                else :
                    $image = imagecreatefromjpeg($destinationPath.$fileName);
                    imagejpeg($image, $destinationPath.$fileName, 70);
                endif;*/
                $requestData['image'] = '/'.$destinationPath.$fileName;
            }

            if (isset($requestData['is_instruction'])) {
                $requestData['is_instruction'] = 1;
            } else {
                $requestData['is_instruction'] = 0;
            }

            return $solution->update($requestData);
        }

        return false;
    }

    /**
     * Delete a solution
     */
    public function destroy($id): bool
    {
        $solution = $this->getRecord($id);
        if ($solution) {
            $solution->forceDelete();
        }

        return false;
    }
}
