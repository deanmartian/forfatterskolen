<?php

namespace App\Repositories\Services;

use App\PageAccess;

class PageAccessService
{
    /**
     * Store the pageAccess model in this var
     *
     * @var PageAccess
     */
    protected $pageAccess;

    /**
     * PageAccessService constructor.
     */
    public function __construct(PageAccess $pageAccess)
    {
        $this->pageAccess = $pageAccess;
    }

    /**
     * Create the access page for the admin
     *
     * @param  $admin_id  int user id of the admin
     * @param  $request  object
     * @return bool|string
     */
    public function createAccessPage($admin_id, $request)
    {
        // delete the access page if exist
        $this->pageAccess->where('user_id', $admin_id)->delete();
        if (isset($request->pages)) {
            foreach ($request->pages as $page) {
                $this->pageAccess->create(['user_id' => $admin_id, 'page_id' => $page]);
            }

            return true;
        }

        return false;
    }
}
