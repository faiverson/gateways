<?php

namespace faiverson\gateways\exceptions;

class RepositoryException extends \Exception
{
    /**
     * @var int
     */
    protected $statusCode = 300;

    /**
     * @param string  $message
     * @param int $statusCode
     */
    public function __construct($message = 'An error occurred', $statusCode = null)
    {
        parent::__construct($message);

        if (! is_null($statusCode)) {
            $this->setStatusCode($statusCode);
        }
    }

    /**
     * @param int $statusCode
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    /**
     * @return int the status code
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
