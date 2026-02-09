<?php


namespace Turted\TurtedBundle\Command;


use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Turted\TurtedBundle\Exceptions\DispatchFailedException;
use Turted\TurtedBundle\Service\TurtedPushService;

#[AsCommand(name: 'turted:push', description: 'Push data to server')]
class TurtedPushCommand extends Command
{
    private TurtedPushService $pushService;

    public function __construct(TurtedPushService $pushService)
    {
        parent::__construct();
        $this->pushService = $pushService;
    }

    protected function configure(): void
    {
        $this
            ->addOption('user', 'u', InputOption::VALUE_REQUIRED, 'User to send the push to')
            ->addOption('event', 'evt', InputOption::VALUE_REQUIRED, 'Event name')
            ->addOption('payload', 'p', InputOption::VALUE_REQUIRED, 'Simple payload string or json')
            ->addOption('channel', 'c', InputOption::VALUE_OPTIONAL, 'Channel to send the push to');
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $helper = $this->getHelper('question');
        $user = $input->getOption('user');
        if (!$user) {
            $question = new Question('Who should receive the push message?', 'Anonymous');
            $input->setOption('user', $helper->ask($input, $output, $question));
        }

        $event = $input->getOption('event');
        if (!$event) {
            $question = new Question('What ist the event name? ', 'Anonymous');
            $input->setOption('event', $helper->ask($input, $output, $question));
        }

        parent::interact($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $user = $input->getOption('user');
        $channel = $input->getOption('channel');
        $event = $input->getOption('event');
        $payload = $input->getOption('payload');

        $data = json_decode($payload, true);
        if ($data === null) {
            $data = ['data' => $payload];
        }
        try {
            $this->pushService->notifyUser($user, $event, $data);
            if ($channel) {
                $this->pushService->notifyChannel($channel, $event, $data);
            }
        } catch (DispatchFailedException $exception) {
            $io->error($exception->getMessage().'. Try -vvvv for debugging info');

            return Command::FAILURE;
        }

        $io->success('Sent');
        return Command::SUCCESS;
    }
}
