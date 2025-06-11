<?php

namespace App\Helpers;

class ApiException extends \Exception
{
    protected $data;

    public function __construct($message = '', $data = '', $code = 0, ?\Exception $previous = null)
    {
        $this->data = $data;

        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the error data
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
}
