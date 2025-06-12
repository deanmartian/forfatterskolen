<?php

namespace App\Helpers;

use App\Http\AdminHelpers;

class DapulseRepository
{
    /**
     * Get all the users
     */
    public static function getUsers(): ApiException
    {
        $method = 'GET';
        $url = 'https://api.dapulse.com:443/v1/users.json';
        $get = [
            'api_key' => '3e8427b0d044ceae9672d5928962c9e2',
            'page' => 1,
        ];

        $response = AdminHelpers::callAPI($method, $url, $get);

        if ($response['http_code'] != 200) {

            $message = ApiResponse::getError($response);

            return new ApiException($message, '', $response['http_code']);
        }

        return $response['data'];
    }

    /**
     * Get all boards
     */
    public function getBoards(): ApiException
    {
        $method = 'GET';
        $url = 'https://api.dapulse.com:443/v1/boards.json';
        $get = [
            'api_key' => '3e8427b0d044ceae9672d5928962c9e2',
        ];

        $response = AdminHelpers::callAPI($method, $url, $get);

        if ($response['http_code'] != 200) {

            $message = ApiResponse::getError($response);

            return new ApiException($message, '', $response['http_code']);
        }

        return $response['data'];
    }

    /**
     * Get specific board
     */
    public function getBoard($board_id): ApiException
    {
        $method = 'GET';
        $url = 'https://api.dapulse.com:443/v1/boards/'.$board_id.'.json';
        $get = [
            'api_key' => '3e8427b0d044ceae9672d5928962c9e2',
        ];

        $response = AdminHelpers::callAPI($method, $url, $get);

        if ($response['http_code'] != 200) {

            $message = ApiResponse::getError($response);

            return new ApiException($message, '', $response['http_code']);
        }

        return $response['data'];
    }

    /**
     * Get board pulses
     */
    public static function getBoardPulses($board_id): ApiException
    {
        $method = 'GET';
        $url = 'https://api.dapulse.com:443/v1/boards/'.$board_id.'/pulses.json';
        $get = [
            'api_key' => '3e8427b0d044ceae9672d5928962c9e2',
        ];

        $response = AdminHelpers::callAPI($method, $url, $get);

        if ($response['http_code'] != 200) {

            $message = ApiResponse::getError($response);

            return new ApiException($message, '', $response['http_code']);
        }

        return $response['data'];
    }

    /**
     * Add user to pulse
     */
    public function addUserToPulse($pulse_id): ApiException
    {
        if ($pulse_id) {
            $method = 'PUT';
            $url = 'https://api.dapulse.com:443//v1/'.$pulse_id.'/{id}/subscribers.json';
            $get = [
                'api_key' => '3e8427b0d044ceae9672d5928962c9e2',
            ];

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
     */
    public function getBoardColumns(): ApiException
    {
        $method = 'GET';
        $url = 'https://api.dapulse.com:443/v1/boards/68370805/columns.json';
        $get = [
            'api_key' => '3e8427b0d044ceae9672d5928962c9e2',
        ];

        $response = AdminHelpers::callAPI($method, $url, $get);

        if ($response['http_code'] != 200) {

            $message = ApiResponse::getError($response);

            return new ApiException($message, '', $response['http_code']);
        }

        return $response['data'];
    }

    /**
     * Assign owner to a pulse
     */
    public function assignUserToPulse($board_id, $pulse_id, $user_id): ApiException
    {
        $method = 'PUT';
        $url = 'https://api.dapulse.com:443/v1/boards/'.$board_id.'/columns/person/person.json';
        $put = [
            'api_key' => '3e8427b0d044ceae9672d5928962c9e2',
            'pulse_id' => $pulse_id,
            'user_id' => $user_id,
        ];

        $response = AdminHelpers::callAPI($method, $url, $put);

        if ($response['http_code'] != 200) {

            $message = ApiResponse::getError($response);

            return new ApiException($message, '', $response['http_code']);
        }

        return $response['data'];
    }

    /**
     * Add new board
     *
     * @param  $data  object passed by controller
     */
    public function addBoard($data): ApiException
    {
        $method = 'POST';
        $url = 'https://api.dapulse.com:443/v1/boards.json';
        $post = [
            'api_key' => '3e8427b0d044ceae9672d5928962c9e2',
            'user_id' => $data->owner,
            'name' => $data->board_name,
            'description' => $data->description,
        ];

        $response = AdminHelpers::callAPI($method, $url, $post);

        if ($response['http_code'] != 201) {

            $message = ApiResponse::getError($response);

            return new ApiException($message, '', $response['http_code']);
        }

        return $response['data'];
    }

    /**
     * Add new pulse to board
     *
     * @param  $board_id  int id of the board
     * @param  $data  object passed by controller
     */
    public function addPulseToBoard($board_id, $data): ApiException
    {
        $method = 'POST';
        $url = 'https://api.dapulse.com:443/v1/boards/'.$board_id.'/pulses.json';
        $post = [
            'api_key' => '3e8427b0d044ceae9672d5928962c9e2',
            'user_id' => $data->user_id, // the id who created the pulse
            'pulse[name]' => $data->pulse_name,
            'group_id' => $data->group_id,
        ];

        $response = AdminHelpers::callAPI($method, $url, $post);

        if ($response['http_code'] != 201) {

            $message = ApiResponse::getError($response);

            return new ApiException($message, '', $response['http_code']);
        }

        return $response['data'];
    }

    /**
     * Update board group title
     */
    public function updateGroupTitle($board_id, $data): ApiException
    {
        $method = 'PUT';
        $url = 'https://api.dapulse.com:443/v1/boards/'.$board_id.'/groups.json';
        $put = [
            'api_key' => '3e8427b0d044ceae9672d5928962c9e2',
            'group_id' => $data->group_id,
            'title' => $data->title,
        ];

        $response = AdminHelpers::callAPI($method, $url, $put);

        if ($response['http_code'] != 200) {

            $message = ApiResponse::getError($response);

            return new ApiException($message, '', $response['http_code']);
        }

        return $response['data'];
    }

    /**
     * Update title of a pulse
     */
    public function updatePulseTitle($pulse_id, $data): ApiException
    {
        $method = 'PUT';
        $url = 'https://api.dapulse.com:443/v1/pulses/'.$pulse_id.'.json';
        $put = [
            'api_key' => '3e8427b0d044ceae9672d5928962c9e2',
            'name' => $data->name,
        ];

        $response = AdminHelpers::callAPI($method, $url, $put);

        if ($response['http_code'] != 200) {

            $message = ApiResponse::getError($response);

            return new ApiException($message, '', $response['http_code']);
        }

        return $response['data'];
    }

    public function removePulseSubscriber($data)
    {
        $method = 'DELETE';
        $url = 'https://api.dapulse.com:443/v1/pulses/'.$data->pulse_id.'/subscribers/'.$data->user_id.'.json';
        $put = [
            'api_key' => '3e8427b0d044ceae9672d5928962c9e2',
        ];

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
     */
    public function setPulseStatus($board_id, $pulse_id, $phase): ApiException
    {
        $method = 'PUT';
        $url = 'https://api.dapulse.com:443/v1/boards/'.$board_id.'/columns/status/status.json';
        $put = [
            'api_key' => '3e8427b0d044ceae9672d5928962c9e2',
            'pulse_id' => $pulse_id,
            'color_index' => $phase,
        ];

        $response = AdminHelpers::callAPI($method, $url, $put);

        if ($response['http_code'] != 200) {

            $message = ApiResponse::getError($response);

            return new ApiException($message, '', $response['http_code']);
        }

        return $response['data'];
    }

    /**
     * Set pulse timeline
     */
    public function setTimeline($board_id, $pulse_id, $from, $to): ApiException
    {
        $method = 'PUT';
        $url = 'https://api.dapulse.com:443/v1/boards/'.$board_id.'/columns/timeline/timeline.json';
        $put = [
            'api_key' => '3e8427b0d044ceae9672d5928962c9e2',
            'pulse_id' => $pulse_id,
            'from' => $from,
            'to' => $to,
        ];

        $response = AdminHelpers::callAPI($method, $url, $put);

        if ($response['http_code'] != 200) {

            $message = ApiResponse::getError($response);

            return new ApiException($message, '', $response['http_code']);
        }

        return $response['data'];
    }
}
