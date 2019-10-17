<?php

namespace Turted\TurtedBundle\Service;


use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Turted\TurtedBundle\ValueObject\Dispatch;

class DispatcherTest extends TestCase
{

    public function testDispatch()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $fileGetContentsWrapper = $this->createMock(FileGetContentsWrapper::class);

        $url = 'http://127.0.0.1:19195';
        $timeout = 3;

        $dispatcher = new Dispatcher($fileGetContentsWrapper, $logger);
        $dispatch = new Dispatch('lala', ['users' => ['Anonymous']], ['no' => 'data'], ['password' => 'none']);
        $httpOptions = [
            'http' => [
                'header' => "Content-type: application/json\r\n",
                'method' => 'POST',
                'content' => json_encode($dispatch->asArray()),
                'timeout' => $timeout,
            ],
        ];
        $context = stream_context_create($httpOptions);

        $fileGetContentsWrapper->expects($this->once())
            ->method('fileGetContents')
            ->with($this->equalTo($url), $this->equalTo(false), $this->anything())
            ->willReturn('ok');

        $result = $dispatcher->dispatch($dispatch, $url, $timeout);
    }
}
