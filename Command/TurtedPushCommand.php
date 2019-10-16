<?php


namespace Turted\TurtedBundle\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Turted\TurtedBundle\Exceptions\DispatchFailedException;
use Turted\TurtedBundle\Service\TurtedPushService;

class TurtedPushCommand extends Command
{
    /**
     * @var TurtedPushService
     */
    private $pushService;

    protected static $defaultName = 'turted:push';

    public function __construct(TurtedPushService $pushService)
    {
        parent::__construct();
        $this->pushService = $pushService;
    }

    protected function configure()
    {
        $this->setDescription('Push data to server')
            ->addOption('user', 'u', InputOption::VALUE_REQUIRED, 'User to send the push to')
            ->addOption('event', 'evt', InputOption::VALUE_REQUIRED, 'Event name')
            ->addOption('payload', 'p', InputOption::VALUE_REQUIRED, 'Simple payload string or json');
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $user = $input->getOption('user');
        if (!$user) {
            $question = new Question('Who should receive the push message?', 'Anonymous');
            $input->setOption('user', $helper->ask($input, $output, $question));
        }

        parent::interact($input, $output);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $user = $input->getOption('user');
        $event = $input->getOption('event');
        $payload = $input->getOption('payload');

        $data = json_decode($payload, true);
        if ($data === null) {
            $data = ['data' => $payload];
        }
        try {
            $this->pushService->notifyUser($user, $event, $data);
        } catch (DispatchFailedException $exception) {
            $io->error($exception->getMessage().'. Try -vvvv for debugging info');

            return;
        }

        $io->success('Sent');
    }
}