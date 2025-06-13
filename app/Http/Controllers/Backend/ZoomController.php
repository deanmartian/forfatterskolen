<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\ApiResponse;
use App\Helpers\ZoomApi;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ZoomController extends Controller
{
    private $zoomUrl = 'https://api.zoom.us/v2';

    // list users
    public function index()
    {
        $zoom = new ZoomApi;
        $response = $zoom->processCurl('GET', $this->zoomUrl.'/users');

        if ($response['http_code'] != 200) {
            $message = ApiResponse::getError($response);
            abort($response['http_code'], $message);
        }

        return $response['data']->users; // aezFfwchSIK314YnVm5vJg
    }

    /**
     * List webinars for particular user
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function webinars($user_id): View
    {
        \Session::put('zoom_user_id', $user_id);
        $zoom = new ZoomApi;
        $response = $zoom->processCurl('GET', $this->zoomUrl.'/users/'.$user_id.'/webinars');

        if ($response['http_code'] != 200) {
            $message = ApiResponse::getError($response);
            abort($response['http_code'], $message);
        }

        $webinars = $response['data']->webinars;

        return view('backend.zoom.webinars.index', compact('webinars', 'user_id'));
    }

    /**
     * Display the create webinar page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function createWebinar($user_id): View
    {
        $webinar = [
            'topic' => '',
            'start_time' => '',
            'agenda' => '',
            'host_video' => '',
            'panelists_video' => '',
            'hd_video' => '',
            'show_share_button' => '',
            'allow_multiple_devices' => '',
            'close_registration' => '',
            'approval_type' => '',
            'audio' => '',
        ];

        return view('backend.zoom.webinars.create', compact('user_id', 'webinar'));
    }

    /**
     * Create new webinar
     */
    public function storeWebinar($user_id, Request $request): RedirectResponse
    {
        $zoom = new ZoomApi;

        $postData = $request->except('_token');
        $postData['timezone'] = 'Asia/Singapore';
        $postData['start_time'] = date('c', strtotime($postData['start_time'])); // convert the date-time
        $postData['settings'] = [
            'host_video' => isset($postData['host_video']) ? 1 : 0,
            'audio' => $postData['audio'],
            'panelists_video' => isset($postData['panelists_video']) ? 1 : 0,
            'hd_video' => isset($postData['hd_video']) ? 1 : 0,
            'show_share_button' => isset($postData['show_share_button']) ? 1 : 0,
            'allow_multiple_devices' => isset($postData['allow_multiple_devices']) ? 1 : 0,
            'close_registration' => isset($postData['close_registration']) ? 1 : 0,
            'approval_type' => $postData['approval_type'],
        ];

        $unsetData = ['host_video', 'panelists_video', 'hd_video', 'show_share_button', 'allow_multiple_devices',
            'close_registration', 'audio', 'approval_type'];

        foreach ($unsetData as $unset) {
            unset($postData[$unset]);
        }

        $response = $zoom->processCurl('POST', $this->zoomUrl.'/users/'.$user_id.'/webinars', $postData);
        if ($response['http_code'] != 201) {
            $message = ApiResponse::getError($response);
            abort($response['http_code'], $message);
        }

        return redirect()->route('admin.zoom.webinars', $user_id);
    }

    /**
     * Display the webinars edit page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editWebinar($webinar_id): View
    {
        $zoom = new ZoomApi;
        $response = $zoom->processCurl('GET', $this->zoomUrl.'/webinars/'.$webinar_id);
        if ($response['http_code'] != 200) {
            $message = ApiResponse::getError($response);
            abort($response['http_code'], $message);
        }

        $panelistResponse = $zoom->processCurl('GET', $this->zoomUrl.'/webinars/'.$webinar_id.'/panelists');
        if ($panelistResponse['http_code'] != 200) {
            $message = ApiResponse::getError($panelistResponse);
            abort($panelistResponse['http_code'], $message);
        }

        $approvedRegistrantResponse = $zoom->processCurl('GET', $this->zoomUrl.'/webinars/'.$webinar_id.'/registrants');
        if ($approvedRegistrantResponse['http_code'] != 200) {
            $message = ApiResponse::getError($approvedRegistrantResponse);
            abort($approvedRegistrantResponse['http_code'], $message);
        }

        $pendingQuery = ['status' => 'pending', 'page_size' => 1];

        $pendingRegistrantResponse = $zoom->processCurl('GET', $this->zoomUrl.'/webinars/'.$webinar_id.'/registrants', $pendingQuery);
        if ($pendingRegistrantResponse['http_code'] != 200) {
            $message = ApiResponse::getError($pendingRegistrantResponse);
            abort($pendingRegistrantResponse['http_code'], $message);
        }

        $deniedQuery = ['status' => 'denied'];

        $deniedRegistrantResponse = $zoom->processCurl('GET', $this->zoomUrl.'/webinars/'.$webinar_id.'/registrants', $deniedQuery);
        if ($deniedRegistrantResponse['http_code'] != 200) {
            $message = ApiResponse::getError($deniedRegistrantResponse);
            abort($deniedRegistrantResponse['http_code'], $message);
        }

        $webinar = (array) $response['data'];
        $panelists = $panelistResponse['data']->panelists;
        $approvedRegistrants = $approvedRegistrantResponse['data'];
        $pendingRegistrants = $pendingRegistrantResponse['data'];
        $deniedRegistrants = $deniedRegistrantResponse['data'];
        $total_records = $approvedRegistrants->total_records + $pendingRegistrants->total_records
            + $deniedRegistrants->total_records;

        return view('backend.zoom.webinars.edit', compact('webinar', 'panelists',
            'approvedRegistrants', 'pendingRegistrants', 'deniedRegistrants', 'total_records'));
    }

    /**
     * Update the webinar
     */
    public function updateWebinar($webinar_id, Request $request): RedirectResponse
    {
        $zoom = new ZoomApi;

        $postData = $request->except('_token');
        $postData['timezone'] = 'Asia/Singapore';
        $postData['start_time'] = date('c', strtotime($postData['start_time'])); // convert the date-time
        $postData['settings'] = [
            'host_video' => isset($postData['host_video']) ? 1 : 0,
            'audio' => $postData['audio'],
            'panelists_video' => isset($postData['panelists_video']) ? 1 : 0,
            'hd_video' => isset($postData['hd_video']) ? 1 : 0,
            'show_share_button' => isset($postData['show_share_button']) ? 1 : 0,
            'allow_multiple_devices' => isset($postData['allow_multiple_devices']) ? 1 : 0,
            'close_registration' => isset($postData['close_registration']) ? 1 : 0,
            'approval_type' => $postData['approval_type'],
        ];

        $unsetData = ['host_video', 'panelists_video', 'hd_video', 'show_share_button', 'allow_multiple_devices',
            'close_registration', 'audio', 'approval_type'];

        foreach ($unsetData as $unset) {
            unset($postData[$unset]);
        }

        $response = $zoom->processCurl('PATCH', $this->zoomUrl.'/webinars/'.$webinar_id, $postData);
        if ($response['http_code'] != 204) {
            $message = ApiResponse::getError($response);
            abort($response['http_code'], $message);
        }

        return redirect()->back();

    }

    /**
     * Delete the webinar
     */
    public function deleteWebinar($webinar_id): RedirectResponse
    {
        $zoom = new ZoomApi;
        $user_id = \Session::get('zoom_user_id'); // get the set user id from the webinar list
        $response = $zoom->processCurl('DELETE', $this->zoomUrl.'/webinars/'.$webinar_id);
        if ($response['http_code'] != 204) {
            $message = ApiResponse::getError($response);
            abort($response['http_code'], $message);
        }

        return redirect()->route('admin.zoom.webinars', $user_id);
    }

    /**
     * Create new panelist for particular webinar
     */
    public function storePanelist($webinar_id, Request $request): RedirectResponse
    {
        $zoom = new ZoomApi;

        $postData['panelists'] = [
            [
                'name' => $request->name,
                'email' => $request->email,
            ],
        ];

        $response = $zoom->processCurl('POST', $this->zoomUrl.'/webinars/'.$webinar_id.'/panelists', $postData);
        if ($response['http_code'] != 201) {
            $message = ApiResponse::getError($response);
            abort($response['http_code'], $message);
        }

        return redirect()->route('admin.zoom.webinar.edit', $webinar_id);
    }

    /**
     * Delete a panelist for certain webinar
     */
    public function deletePanelist($webinar_id, $panelist_id): RedirectResponse
    {
        $zoom = new ZoomApi;

        $response = $zoom->processCurl('DELETE', $this->zoomUrl.'/webinars/'.$webinar_id.'/panelists/'.$panelist_id);
        if ($response['http_code'] != 204) {
            $message = ApiResponse::getError($response);
            abort($response['http_code'], $message);
        }

        return redirect()->route('admin.zoom.webinar.edit', $webinar_id);
    }
}
