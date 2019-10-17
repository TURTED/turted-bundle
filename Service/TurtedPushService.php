<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 15.04.2016
 * Time: 17:24.
 */

namespace Turted\TurtedBundle\Service;

use Psr\Log\LoggerInterface;
use Turted\TurtedBundle\Exceptions\DispatchFailedException;
use Turted\TurtedBundle\ValueObject\Dispatch;

class TurtedPushService
{
    private $url;

    private $password;

    private $timeout;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Dispatcher
     */
    private $dispatcher;

    public function __construct(
        $config,
        LoggerInterface $turtedLogger,
        Dispatcher $dispatcher
    ) {
        $this->url = $config['url'];
        $this->password = $config['password'];
        $this->logger = $turtedLogger;
        $this->timeout = $config['timeout'];
        $this->dispatcher = $dispatcher;
    }

    /**
     * Options (url: for dedicated server to push to, auth: for providing auth data)
     *
     * @param $username
     * @param $event
     * @param $payload
     *
     * @param array $options
     * @return bool|string
     * @throws DispatchFailedException
     */
    public function notifyUser($username, $event, $payload, $options = [])
    {
        return $this->notifyTargets(['users' => [$username]], $event, $payload, $options);
    }

    /**
     * Options (url: for dedicated server to push to, auth: for providing auth data)
     *
     * @param $channel
     * @param $event
     * @param $payload
     *
     * @param array $options
     * @return bool|string
     * @throws DispatchFailedException
     */
    public function notifyChannel($channel, $event, $payload, $options = [])
    {
        return $this->notifyTargets(['channel' => $channel], $event, $payload, $options);
    }


    /**
     * @param $targets
     * @param $event
     * @param $payload
     *
     * @param $options
     * @return bool
     * @throws DispatchFailedException
     */
    private function notifyTargets($targets, $event, $payload, $options)
    {
        // default auth: password
        $auth = ['password' => $this->password];
        if (isset($options['auth'])) {
            $auth = $options['auth'];
        }

        if (isset($targets['users'])) {
            $usernames = $targets['users'];
            foreach ($usernames as $username) {
                $this->logger->debug('Notify user "'.$username.'" of event "'.$event.'"');
            }
        }

        $dispatch = new Dispatch($event, $targets, $payload, $auth);

        $server = $this->url;
        // allow server to be overwritten by options
        if (isset($options['url'])) {
            $server = $options['url'];
        }

        try {
            $result = $this->dispatcher->dispatch($dispatch, $server, $this->timeout);
            $this->logger->debug('PUSH server reply: '.$result);
        } catch (DispatchFailedException $exception) {
            $this->logger->error(
                'PUSH ERROR '.$server.'|'.$exception->getMessage(),
                [
                    'err' => $exception->getError(),
                    'pushed' => json_encode($dispatch->asInfoArray()),
                ]
            );

            throw $exception;
        }

        return true;
    }
}
