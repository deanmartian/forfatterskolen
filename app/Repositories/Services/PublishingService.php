<?php
namespace App\Repositories\Services;

use App\Publishing;

class PublishingService {

    /**
     * Store the publishing model in this var
     * @var Publishing
     */
    protected $publishing;

    /**
     * Table fields
     * @var array
     */
    protected $fields = [
        'publishing'            => '',
        'mail_address'          => '',
        'visiting_address'      => '',
        'phone'                 => '',
        'genre'                 => '',
        'email'                 => '',
        'home_link'             => '',
        'send_manuscript_link'  => ''
    ];

    /**
     * PublishingService constructor.
     * @param Publishing $publishing
     */
    public function __construct(Publishing $publishing)
    {
        $this->publishing = $publishing;
    }

    /**
     * Get the fields
     * @return array
     */
    public function fields()
    {
        return $this->fields;
    }

    /**
     * Create new publisher house
     * @param array $data data to be inserted
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function store(array $data)
    {
        $data['genre'] = implode(', ', $data['genre']);
        return $this->publishing->create($data);
    }

    /**
     * Update publishing house
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, array $data)
    {
        $publishingHouse = $this->find($id);
        if ($publishingHouse) {
            $data['genre'] = implode(', ', $data['genre']);
            return $publishingHouse->update($data);
        }

        return false;
    }

    /**
     * Delete record
     * @param $id
     * @return bool|null
     */
    public function destroy($id)
    {
        $publishingHouse = $this->find($id);
        if ($publishingHouse) {
            return $publishingHouse->forceDelete();
        }

        return false;
    }

    /**
     * Find publishing house
     * @param $id
     * @return \App\Publishing
     */
    public function find($id)
    {
        return $this->publishing->find($id);
    }

    /**
     * Set the pagination for this model
     * @param int $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($perPage = 15)
    {
        return $this->publishing->orderBy('publishing', 'ASC')->paginate($perPage);
    }

    /**
     * Search term on publishing and genre fields
     * @param $term
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function search($term)
    {
        return $this->publishing->where('publishing', 'LIKE', '%' . $term  . '%')
            ->orWhere('genre', 'LIKE', '%' . $term  . '%')
            ->get();
    }

}