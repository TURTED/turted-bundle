<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 15.04.2016
 * Time: 17:24.
 */

namespace Turted\Bundle\Service;

use Psr\Log\LoggerInterface;

class TurtedRestPushService
{
    public function __construct($config, LoggerInterface $logger)
    {
        $this->url = $config['push']['url'];
        $this->password = $config['push']['password'];
        $this->logger = $logger;
        if (isset($config['push']['timeout'])) {
            $this->timeout = $config['push']['timeout'];
        } else {
            $this->timeout = 5;
        }
    }

    /**
     * @param $username
     * @param $event
     * @param $payload
     * Optional param options (url: for dedicated server to push to, auth: for providing auth data)
     *
     * @return bool|string
     */
    public function notifyUser($username, $event, $payload)
    {
        $options = [];
        $this->logger->debug('Notify '.$username.' of event '.$event);
        $options = [];
        if (func_num_args() > 3) {
            $options = func_get_arg(3);
            $this->logger->debug('Additional options in notifyUser');
        }

        $auth = false;
        if (isset($options['auth'])) {
            $auth = $options['auth'];
        }

        if (is_array($payload)) {
            $cmd = [
                    'cmd' => 'notifyUser',
                    'password' => $this->password,
                    'data' => [
                            'event' => $event,
                            'user' => $username,
                            'payload' => $payload,
                    ],
            ];

            //if provided, add auth data
            if ($auth) {
                $cmd['auth'] = $auth;
            }

            //construct a POST request
            $httpOptions = [
                    'http' => [
                            'header' => "Content-type: application/json\r\n",
                            'method' => 'POST',
                            'content' => json_encode($cmd),
                            'timeout' => $this->timeout,
                    ],
            ];

            $context = stream_context_create($httpOptions);
            //custom logic to determine target server

            $server = $this->url;
            if (isset($options['url'])) {
                $server = $options['url'];
            }
            $this->logger->debug('PUSH to server '.$server);
            $result = @file_get_contents($server, false, $context);
            if ($result === false) {
                $error = error_get_last();
                $msg = '<unknown reason>';
                if ($error) {
                    $msg = $error['message'];
                }
                $this->logger->critical('PUSH ERROR '.$server.'|'.$msg, [
                        'err' => $error,
                        'pushed' => json_encode($cmd),
                ]);

                return 'Error sending message';
            }
            $this->logger->debug('PUSH server reply: '.$result);

            return true;
        }

        return false;
    }
}
