<?php

namespace App\Repositories\Services;

use App\Http\Requests\AddWritingGroupRequest;
use App\WritingGroup;

class WritingGroupService
{
    /**
     * Model storage
     *
     * @var WritingGroup
     */
    protected $writingGroup;

    /**
     * Fields list
     *
     * @var array
     */
    protected $fields = [
        'id' => '',
        'name' => '',
        'contact_id' => '',
        'description' => '',
        'group_photo' => '',
        'next_meeting' => '',
    ];

    /**
     * WritingGroupService constructor.
     */
    public function __construct(WritingGroup $writingGroup)
    {
        $this->writingGroup = $writingGroup;
    }

    /**
     * Get the table fields
     */
    public function fields(): array
    {
        return $this->fields;
    }

    /**
     * Get single or paginated records
     *
     * @param  null  $id
     * @param  null  $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function getRecord($id = null, $page = null)
    {
        if (! is_null($id)) {
            return $this->writingGroup->find($id);
        }

        return $this->writingGroup->paginate($page ? $page : 15);
    }

    /**
     * Insert writing group
     */
    public function store(AddWritingGroupRequest $data)
    {
        $storeRequest = $data->all();
        if ($data->hasFile('group_photo')) {
            $destinationPath = 'storage/writing-group/'; // upload path
            $extension = $data->group_photo->extension(); // getting image extension
            $fileName = time().'.'.$extension; // renaming image
            $data->group_photo->move($destinationPath, $fileName);
            // optimize image
            if (strtolower($extension) == 'png') {
                $image = imagecreatefrompng($destinationPath.$fileName);
                imagepng($image, $destinationPath.$fileName, 9);
            } else {
                $image = imagecreatefromjpeg($destinationPath.$fileName);
                imagejpeg($image, $destinationPath.$fileName, 70);
            }
            $storeRequest['group_photo'] = '/'.$destinationPath.$fileName;
        }

        $this->writingGroup->create($storeRequest);
    }

    /**
     * Update writing group
     *
     * @param  $id  int
     */
    public function update($id, AddWritingGroupRequest $data)
    {
        $writingGroup = $this->getRecord($id);
        if ($writingGroup) {
            $updateWritingGroup = $data->all();
            if ($data->hasFile('group_photo')) {
                $destinationPath = 'storage/writing-group/'; // upload path
                $extension = $data->group_photo->extension(); // getting image extension
                $fileName = time().'.'.$extension; // renaming image
                $data->group_photo->move($destinationPath, $fileName);
                // optimize image
                if (strtolower($extension) == 'png') {
                    $image = imagecreatefrompng($destinationPath.$fileName);
                    imagepng($image, $destinationPath.$fileName, 9);
                } else {
                    $image = imagecreatefromjpeg($destinationPath.$fileName);
                    imagejpeg($image, $destinationPath.$fileName, 70);
                }
                $updateWritingGroup['group_photo'] = '/'.$destinationPath.$fileName;
            }
            $writingGroup->update($updateWritingGroup);
        }
    }

    /**
     * Delete a writing group
     *
     * @param  $id  int
     */
    public function destroy($id): bool
    {
        $writingGroup = $this->getRecord($id);
        if ($writingGroup) {
            // check if has image to prevent unlink permission error
            if ($writingGroup->group_photo) {
                $filePath = str_replace('public ', 'public', public_path().$writingGroup->group_photo);
                if (file_exists($filePath)) {
                    unlink($filePath); // delete the physical file
                }
            }
            $writingGroup->forceDelete();
        }

        return false;
    }
}
