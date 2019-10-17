<?php


namespace Turted\TurtedBundle\Service;


use Psr\Log\LoggerInterface;
use Turted\TurtedBundle\Exceptions\DispatchFailedException;
use Turted\TurtedBundle\ValueObject\Dispatch;

class Dispatcher
{
    /**
     * @var FileGetContentsWrapper
     */
    private $fileGetContentsWrapper;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(FileGetContentsWrapper $fileGetContentsWrapper, LoggerInterface $logger)
    {
        $this->fileGetContentsWrapper = $fileGetContentsWrapper;
        $this->logger = $logger;
    }

    /**
     * @param Dispatch $dispatch
     * @param $url
     * @param $timeout
     * @return string
     * @throws DispatchFailedException
     */
    public function dispatch(Dispatch $dispatch, $url, $timeout)
    {
        //construct a POST request
        $httpOptions = [
            'http' => [
                'header' => "Content-type: application/json\r\n",
                'method' => 'POST',
                'content' => json_encode($dispatch->asArray()),
                'timeout' => $timeout,
            ],
        ];

        $context = stream_context_create($httpOptions);

        $this->logger->debug('PUSH to server '.$url);
        $result = $this->fileGetContentsWrapper->fileGetContents($url, false, $context);
        if ($result === false) {
            $error = error_get_last();
            $msg = '<unknown reason>';
            if ($error) {
                $msg = $error['message'];
            }

            throw new DispatchFailedException($dispatch, $msg, $error);
        }

        return $result;
    }
}