<?php

namespace App\MessageHandler;

use App\Message\PackageData;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mime\Email;

class PackageDataHandler implements MessageHandlerInterface
{
    protected $logger;
    public function __construct(LoggerInterface $logger)
    {
       $this->logger= $logger;
    }

    public function __invoke(PackageData $message, MailerInterface $mailer, LoggerInterface $logger)
    {
        sleep(5);
        $mailFrom = '';
        $mailTo   = '';
        $this->changeStatus();
        try {
            $email = (new Email())
                ->from($mailFrom)
                ->to($mailTo)
                ->subject('Package processing completed!')
                ->text('Package processing completed!');

            $mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            $logger->error($e->getMessage());
        }

        $this->logger->info('Задаие выполнилось норм' . $message->getContent());
    }
}
