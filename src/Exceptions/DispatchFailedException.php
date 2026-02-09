<?php


namespace Turted\TurtedBundle\Exceptions;


use Turted\TurtedBundle\ValueObject\Dispatch;

class DispatchFailedException extends \Exception
{
    private mixed $error;
    private Dispatch $dispatch;

    public function __construct(Dispatch $dispatch, string $message, mixed $error)
    {
        parent::__construct($message, 0, null);
        $this->error = $error;
        $this->dispatch = $dispatch;
    }

    public function getError(): mixed
    {
        return $this->error;
    }

    public function getDispatch(): Dispatch
    {
        return $this->dispatch;
    }
}
