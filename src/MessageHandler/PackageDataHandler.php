<?php

namespace App\MessageHandler;

use App\Entity\Package;
use App\Message\PackageData;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mime\Email;

class PackageDataHandler implements MessageHandlerInterface
{
    protected const MAIL_FROM ='example@mail.ru';

    protected const MAIL_TO ='myfriend@gmail.com';

    protected $logger;

    protected $mailer;

    protected $entityManager;

    public function __construct(LoggerInterface $logger, MailerInterface $mailer, EntityManagerInterface $entityManager)
    {
        $this->logger = $logger;
        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
    }

    public function __invoke(PackageData $message)
    {
        sleep(5);
        $id = $message->getContent()['id'];
        $this->changeStatusDbToTrue($id);
        $this->sendToEmail($this->mailer, $this->logger);
    }

    /**
     *
     * @param MailerInterface $mailer
     * @param LoggerInterface $logger
     *
     * @return void
     */
    protected function sendToEmail(MailerInterface $mailer, LoggerInterface $logger)
    {
        try {
            $email = (new Email())
                ->from(self::MAIL_FROM)
                ->to(self::MAIL_TO)
                ->subject('Package processing completed!')
                ->text('Package processing completed!');

            $mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            $logger->error($e->getMessage());
        }
        return;
    }

    /**
     *
     * @param int $id
     */
    protected function changeStatusDbToTrue(int $id)
    {
        $entityManager = $this->entityManager;
        $package = $entityManager->getRepository(Package::class)->find($id);

        if (!$package) {
            throw $this->createNotFoundException(
                'No package found for id '.$id
            );
        }

        $package->setStatus(true);
        $entityManager->flush();
    }
}
