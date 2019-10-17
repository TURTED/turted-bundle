<?php


namespace Turted\TurtedBundle\ValueObject;


class Dispatch
{
    private $event;
    private $targets;
    private $payload;
    private $auth;

    public function __construct($event, $targets, $payload, $auth)
    {
        $this->event = $event;
        $this->targets = $targets;
        $this->payload = $payload;
        $this->auth = $auth;
    }

    public function asArray()
    {
        return [
            'event' => $this->event,
            'targets' => $this->targets,
            'payload' => $this->payload,
            'auth' => $this->auth,
        ];
    }

    /**
     * Info about dispatch
     *
     * @return array
     */
    public function asInfoArray()
    {
        $data = $this->asArray();
        unset($data['auth']);

        return $data;
    }
}