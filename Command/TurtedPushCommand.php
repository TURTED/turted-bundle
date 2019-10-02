<?php


namespace Turted\TurtedBundle\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Turted\TurtedBundle\Service\TurtedPushService;

class TurtedPushCommand extends Command
{
    /**
     * @var TurtedPushService
     */
    private $pushService;

    public function __construct($name = null)
    {
        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setName('turted:push')
            ->setDescription('Push data to server');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        // $this->pushService->notifyUser('admin', 'login', ['user' => 'nobody']);
        $io->success('Running');

    }

}