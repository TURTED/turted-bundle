<?php


namespace Tests\Service;


use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Config\Definition\Processor;
use Turted\TurtedBundle\DependencyInjection\Configuration;
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

    public function setUp()
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->fileGetContentsWrapper = $this->createMock(FileGetContentsWrapper::class);
        parent::setUp();
    }

    public function testNotifyUser()
    {
        #$this->fileGetContentsWrapper->method('fileGetContents')->willReturn($someSimulatedJson);

        $this->fileGetContentsWrapper->expects($this->once())
            ->method('fileGetContents')
            ->with($this->equalTo('http://127.0.0.1:7117/push/'), $this->equalTo(false), $this->equalTo('asdf'))
            ->willReturn(false);

        $config = $this->getDefaultConfig();
        $push = new TurtedPushService($config, $this->logger, $this->fileGetContentsWrapper);
        $push->notifyUser('xosofox', 'test', ['some' => 'data']);
    }

    private function getDefaultConfig()
    {
        $configuration = new Configuration();
        $treeBuilder = $configuration->getConfigTreeBuilder();
        $tree = $treeBuilder->buildTree();
        $processor = new Processor();

        return $processor->process($tree, []);
    }
}