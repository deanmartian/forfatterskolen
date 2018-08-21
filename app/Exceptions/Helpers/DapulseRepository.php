<?php

namespace App\Helpers;

use App\Http\AdminHelpers;
use Illuminate\Filesystem\FilesystemManager;

class DapulseRepository
{

    /**
     * Get all the users
     * @return ApiException
     */
    public static function getUsers()
    {
        $method = "GET";
        $url = "https://api.dapulse.com:443/v1/users.json";
        $get = array(
            'api_key' => '3e8427b0d044ceae9672d5928962c9e2',
            'page' => 1
        );

        $response = AdminHelpers::callAPI($method, $url, $get);

        if ($response['http_code'] != 200) {

            $message = ApiResponse::getError($response);
            return new ApiException($message, '', $response['http_code']);
        }

        return $response['data'];
    }

    /**
     * Get all boards
     * @return ApiException
     */
    public function getBoards()
    {
        $method = "GET";
        $url = "https://api.dapulse.com:443/v1/boards.json";
        $get = array(
            'api_key' => '3e8427b0d044ceae9672d5928962c9e2'
        );

        $response = AdminHelpers::callAPI($method, $url, $get);

        if ($response['http_code'] != 200) {

            $message = ApiResponse::getError($response);
            return new ApiException($message, '', $response['http_code']);
        }

        return $response['data'];
    }

    /**
     * Get specific board
     * @return ApiException
     */
    public function getBoard($board_id)
    {
        $method = "GET";
        $url = "https://api.dapulse.com:443/v1/boards/".$board_id.".json";
        $get = array(
            'api_key' => '3e8427b0d044ceae9672d5928962c9e2'
        );

        $response = AdminHelpers::callAPI($method, $url, $get);

        if ($response['http_code'] != 200) {

            $message = ApiResponse::getError($response);
            return new ApiException($message, '', $response['http_code']);
        }

        return $response['data'];
    }

    /**
     * Get board pulses
     * @return ApiException
     */
    public static function getBoardPulses($board_id)
    {
        $method = "GET";
        $url = "https://api.dapulse.com:443/v1/boards/".$board_id."/pulses.json";
        $get = array(
            'api_key' => '3e8427b0d044ceae9672d5928962c9e2'
        );

        $response = AdminHelpers::callAPI($method, $url, $get);

        if ($response['http_code'] != 200) {

            $message = ApiResponse::getError($response);
            return new ApiException($message, '', $response['http_code']);
        }

        return $response['data'];
    }

    /**
     * Add user to pulse
     * @param $pulse_id
     * @return ApiException
     */
    public function addUserToPulse($pulse_id)
    {
        if ($pulse_id) {
            $method = "PUT";
            $url = "https://api.dapulse.com:443//v1/".$pulse_id."/{id}/subscribers.json";
            $get = array(
                'api_key' => '3e8427b0d044ceae9672d5928962c9e2'
            );

            $response = AdminHelpers::callAPI($method, $url, $get);

            if ($response['http_code'] != 200) {

                $message = ApiResponse::getError($response);
                return new ApiException($message, '', $response['http_code']);
            }

            return $response['data'];
        }

        return new ApiException('Page not found', '', '500');
    }

    /**
     * Get board columns
     * @return ApiException
     */
    public function getBoardColumns()
    {
        $method = "GET";
        $url = "https://api.dapulse.com:443/v1/boards/68370805/columns.json";
        $get = array(
            'api_key' => '3e8427b0d044ceae9672d5928962c9e2'
        );

        $response = AdminHelpers::callAPI($method, $url, $get);

        if ($response['http_code'] != 200) {

            $message = ApiResponse::getError($response);
            return new ApiException($message, '', $response['http_code']);
        }

        return $response['data'];
    }

    /**
     * Assign owner to a pulse
     * @param $board_id
     * @param $pulse_id
     * @param $user_id
     * @return ApiException
     */
    public function assignUserToPulse($board_id, $pulse_id, $user_id)
    {
        $method = "PUT";
        $url = "https://api.dapulse.com:443/v1/boards/".$board_id."/columns/person/person.json";
        $put = array(
            'api_key' => '3e8427b0d044ceae9672d5928962c9e2',
            'pulse_id' => $pulse_id,
            'user_id' => $user_id
        );

        $response = AdminHelpers::callAPI($method, $url, $put);

        if ($response['http_code'] != 200) {

            $message = ApiResponse::getError($response);
            return new ApiException($message, '', $response['http_code']);
        }

        return $response['data'];
    }

    /**
     * Add new board
     * @param $data object passed by controller
     * @return ApiException
     */
    public function addBoard($data)
    {
        $method = "POST";
        $url = "https://api.dapulse.com:443/v1/boards.json";
        $post = array(
            'api_key'       => '3e8427b0d044ceae9672d5928962c9e2',
            'user_id'       => $data->owner,
            'name'          => $data->board_name,
            'description'   => $data->description
        );

        $response = AdminHelpers::callAPI($method, $url, $post);

        if ($response['http_code'] != 201) {

            $message = ApiResponse::getError($response);
            return new ApiException($message, '', $response['http_code']);
        }

        return $response['data'];
    }

    /**
     * Add new pulse to board
     * @param $board_id int id of the board
     * @param $data object passed by controller
     * @return ApiException
     */
    public function addPulseToBoard($board_id, $data)
    {
        $method = "POST";
        $url = "https://api.dapulse.com:443/v1/boards/".$board_id."/pulses.json";
        $post = array(
            'api_key'       => '3e8427b0d044ceae9672d5928962c9e2',
            'user_id'       => $data->user_id, // the id who created the pulse
            'pulse[name]'   => $data->pulse_name,
            'group_id'      => $data->group_id
        );

        $response = AdminHelpers::callAPI($method, $url, $post);

        if ($response['http_code'] != 201) {

            $message = ApiResponse::getError($response);
            return new ApiException($message, '', $response['http_code']);
        }

        return $response['data'];
    }

    /**
     * Update board group title
     * @param $board_id
     * @param $data
     * @return ApiException
     */
    public function updateGroupTitle($board_id, $data)
    {
        $method = "PUT";
        $url = "https://api.dapulse.com:443/v1/boards/".$board_id."/groups.json";
        $put = array(
            'api_key'   => '3e8427b0d044ceae9672d5928962c9e2',
            'group_id'  => $data->group_id,
            'title'     => $data->title
        );

        $response = AdminHelpers::callAPI($method, $url, $put);

        if ($response['http_code'] != 200) {

            $message = ApiResponse::getError($response);
            return new ApiException($message, '', $response['http_code']);
        }

        return $response['data'];
    }

    /**
     * Update title of a pulse
     * @param $pulse_id
     * @param $data
     * @return ApiException
     */
    public function updatePulseTitle($pulse_id, $data)
    {
        $method = "PUT";
        $url = "https://api.dapulse.com:443/v1/pulses/".$pulse_id.".json";
        $put = array(
            'api_key'   => '3e8427b0d044ceae9672d5928962c9e2',
            'name'      => $data->name
        );

        $response = AdminHelpers::callAPI($method, $url, $put);

        if ($response['http_code'] != 200) {

            $message = ApiResponse::getError($response);
            return new ApiException($message, '', $response['http_code']);
        }

        return $response['data'];
    }

    public function removePulseSubscriber($data)
    {
        $method = "DELETE";
        $url = "https://api.dapulse.com:443/v1/pulses/".$data->pulse_id."/subscribers/".$data->user_id.".json";
        $put = array(
            'api_key'   => '3e8427b0d044ceae9672d5928962c9e2'
        );

        $response = AdminHelpers::callAPI($method, $url, $put);
        return $response;
        /*if ($response['http_code'] != 200) {

            $message = ApiResponse::getError($response);
            return new ApiException($message, '', $response['http_code']);
        }

        return $response['data'];*/
    }

    /**
     * Set pulse status
     * @param $board_id
     * @param $pulse_id
     * @param $phase
     * @return ApiException
     */
    public function setPulseStatus($board_id, $pulse_id, $phase)
    {
        $method = "PUT";
        $url = "https://api.dapulse.com:443/v1/boards/".$board_id."/columns/status/status.json";
        $put = array(
            'api_key' => '3e8427b0d044ceae9672d5928962c9e2',
            'pulse_id' => $pulse_id,
            'color_index' => $phase
        );

        $response = AdminHelpers::callAPI($method, $url, $put);

        if ($response['http_code'] != 200) {

            $message = ApiResponse::getError($response);
            return new ApiException($message, '', $response['http_code']);
        }

        return $response['data'];
    }

    /**
     * Set pulse timeline
     * @param $board_id
     * @param $pulse_id
     * @param $from
     * @param $to
     * @return ApiException
     */
    public function setTimeline($board_id, $pulse_id, $from, $to)
    {
        $method = "PUT";
        $url = "https://api.dapulse.com:443/v1/boards/".$board_id."/columns/timeline/timeline.json";
        $put = array(
            'api_key' => '3e8427b0d044ceae9672d5928962c9e2',
            'pulse_id' => $pulse_id,
            'from' => $from,
            'to' => $to
        );

        $response = AdminHelpers::callAPI($method, $url, $put);

        if ($response['http_code'] != 200) {

            $message = ApiResponse::getError($response);
            return new ApiException($message, '', $response['http_code']);
        }

        return $response['data'];
    }
}