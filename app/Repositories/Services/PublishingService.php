<?php

namespace App\Repositories\Services;

use App\Publishing;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

class PublishingService
{
    /**
     * Store the publishing model in this var
     *
     * @var Publishing
     */
    protected $publishing;

    /**
     * Table fields
     *
     * @var array
     */
    protected $fields = [
        'publishing' => '',
        'mail_address' => '',
        'visiting_address' => '',
        'phone' => '',
        'genre' => '',
        'email' => '',
        'home_link' => '',
        'send_manuscript_link' => '',
    ];

    /**
     * PublishingService constructor.
     */
    public function __construct(Publishing $publishing)
    {
        $this->publishing = $publishing;
    }

    /**
     * Get the fields
     */
    public function fields(): array
    {
        return $this->fields;
    }

    /**
     * Create new publisher house
     *
     * @param  array  $data  data to be inserted
     */
    public function store(array $data): Model
    {
        $data['genre'] = implode(', ', $data['genre']);

        return $this->publishing->create($data);
    }

    /**
     * Update publishing house
     */
    public function update(int $id, array $data): bool
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
     */
    public function destroy($id): ?bool
    {
        $publishingHouse = $this->find($id);
        if ($publishingHouse) {
            return $publishingHouse->forceDelete();
        }

        return false;
    }

    /**
     * Find publishing house
     */
    public function find($id): Publishing
    {
        return $this->publishing->find($id);
    }

    /**
     * Set the pagination for this model
     *
     * @param  int  $page
     */
    public function paginate($perPage = 15): LengthAwarePaginator
    {
        return $this->publishing->orderBy('publishing', 'ASC')->paginate($perPage);
    }

    /**
     * Search term on publishing and genre fields
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function search($term)
    {
        return $this->publishing->where('publishing', 'LIKE', '%'.$term.'%')
            ->orWhere('genre', 'LIKE', '%'.$term.'%')
            ->get();
    }
}
