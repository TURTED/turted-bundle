<?php


namespace Turted\TurtedBundle\ValueObject;


class Dispatch
{
    private string $event;
    private array $targets;
    private array|string $payload;
    private array $auth;

    public function __construct(string $event, array $targets, array|string $payload, array $auth)
    {
        $this->event = $event;
        $this->targets = $targets;
        $this->payload = $payload;
        $this->auth = $auth;
    }

    public function asArray(): array
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
     */
    public function asInfoArray(): array
    {
        $data = $this->asArray();
        unset($data['auth']);

        return $data;
    }
}
