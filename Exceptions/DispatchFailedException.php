<?php


namespace Turted\TurtedBundle\Exceptions;


class DispatchFailedException extends \Exception
{
    private $error;

    public function __construct($message, $error)
    {
        parent::__construct($message, 0, null);
        $this->error = $error;
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

}