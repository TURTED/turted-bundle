<?php


namespace Tests\Service;


use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Turted\TurtedBundle\Service\Dispatcher;
use Turted\TurtedBundle\Service\FileGetContentsWrapper;
use Turted\TurtedBundle\Service\TurtedPushService;

class TurtedPushServiceTest extends TestCase
{
    /**
     * @var MockObject|LoggerInterface
     */
    private $logger;

    /**
     * @var MockObject
     */
    private $fileGetContentsWrapper;

    public function setUp(): void
    {
        $this->logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $this->fileGetContentsWrapper = $this->createMock(FileGetContentsWrapper::class);
        parent::setUp();
    }

    public function testNotifyUser()
    {
        $config = [
            'url' => 'http://127.0.0.1:7117/push/',
            'password' => 'asdf',
            'timeout' => 1,
        ];
        $logger = $this->createMock(LoggerInterface::class);
        $dispatcher = $this->createMock(Dispatcher::class);
        $dispatcher->expects($this->once())
            ->method('dispatch');

        $push = new TurtedPushService($config, $this->logger, $dispatcher);
        $push->notifyUser('xosofox', 'test', ['some' => 'data']);
    }
}
