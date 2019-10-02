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
    private $version;

    private $url;

    private $password;

    private $timeout;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var FileGetContentsWrapper
     */
    private $fileGetContentsWrapper;

    /**
     * @var HttpOptionsCreator
     */
    private $httpOptionsCreator;
    /**
     * @var Dispatcher
     */
    private $dispatcher;

    public function __construct(
        $config,
        LoggerInterface $logger,
        Dispatcher $dispatcher
    ) {
        $this->url = $config['url'];
        $this->password = $config['password'];
        $this->logger = $logger;
        $this->timeout = $config['timeout'];
        $this->dispatcher = $dispatcher;
    }

    /**
     * Optional param options (url: for dedicated server to push to, auth: for providing auth data)
     *
     * @param $username
     * @param $event
     * @param $payload
     *
     * @return bool|string
     */
    public function notifyUser($username, $event, $payload, $options = [])
    {
        return $this->notifyTargets(['users' => [$username]], $event, $payload, $options);
    }

    /**
     * Optional param options (url: for dedicated server to push to, auth: for providing auth data)
     *
     * @param $channel
     * @param $event
     * @param $payload
     *
     * @return bool|string
     */
    public function notifyChannel($channel, $event, $payload, $options = [])
    {
        return $this->notifyTargets(["channel" => $channel], $event, $payload, $options);
    }


    /**
     * @param $username
     * @param $event
     * @param $payload
     *
     * @return bool|string
     */
    private function notifyTargets($targets, $event, $payload, $options)
    {
        if (func_num_args() > 3) {
            $options = func_get_arg(3);
            $this->logger->debug('Additional options in notifyUser');
        }

        // default additional auth: repeat password
        $auth = ['password' => $this->password];
        if (isset($options['auth'])) {
            $auth = $options['auth'];
        }

        if (isset($targets['users'])) {
            $usernames = $targets['users'];
            foreach ($usernames as $username) {
                $this->logger->debug('Notify '.$username.' of event '.$event);
            }
        }

        if (is_array($payload)) {
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
                $this->logger->critical(
                    'PUSH ERROR '.$server.'|'.$exception->getMessage(),
                    [
                        'err' => $exception->getError(),
                        'pushed' => json_encode($dispatch->asInfoArray()),
                    ]
                );

                return 'Error sending message';
            }

            return true;
        }

        return false;
    }
}
