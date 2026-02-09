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
    private string $url;

    private string $password;

    private int $timeout;

    private LoggerInterface $logger;

    private Dispatcher $dispatcher;

    public function __construct(
        array $config,
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
     * @throws DispatchFailedException
     */
    public function notifyUser(string $username, string $event, array|string $payload, array $options = []): bool
    {
        return $this->notifyTargets(['users' => [$username]], $event, $payload, $options);
    }

    /**
     * Options (url: for dedicated server to push to, auth: for providing auth data)
     *
     * @throws DispatchFailedException
     */
    public function notifyChannel(string $channel, string $event, array|string $payload, array $options = []): bool
    {
        return $this->notifyTargets(['channels' => [$channel]], $event, $payload, $options);
    }


    /**
     * @throws DispatchFailedException
     */
    private function notifyTargets(array $targets, string $event, array|string $payload, array $options): bool
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
