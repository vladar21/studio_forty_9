<?php

namespace App\InventoryBundle\MessageHandler;

use App\InventoryBundle\Message\StockOutMessage;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

/**
 * Handles the stock out message.
 */
class StockOutMessageHandler implements MessageHandlerInterface
{
    private MailerInterface $mailer;

    /**
     * StockOutMessageHandler constructor.
     *
     * @param MailerInterface $mailer Mailer service for sending emails.
     */
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Invokes the message handler.
     *
     * @param StockOutMessage $message The stock out message.
     * @throws TransportExceptionInterface
     */
    public function __invoke(StockOutMessage $message): void
    {
        $email = (new Email())
            ->from('noreply@yourdomain.com')
            ->to('yourrecipient@domain.com')
            ->subject('Item Out of Stock')
            ->text("The item with SKU {$message->getSku()} is out of stock at the location {$message->getBranch()}.");

        $this->mailer->send($email);
    }
}
