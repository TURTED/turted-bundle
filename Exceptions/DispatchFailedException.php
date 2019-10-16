<?php


namespace Turted\TurtedBundle\Exceptions;


use Turted\TurtedBundle\ValueObject\Dispatch;

class DispatchFailedException extends \Exception
{
    private $error;
    /**
     * @var Dispatch
     */
    private $dispatch;

    public function __construct(Dispatch $dispatch, $message, $error)
    {
        parent::__construct($message, 0, null);
        $this->error = $error;
        $this->dispatch = $dispatch;
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return Dispatch
     */
    public function getDispatch()
    {
        return $this->dispatch;
    }
}