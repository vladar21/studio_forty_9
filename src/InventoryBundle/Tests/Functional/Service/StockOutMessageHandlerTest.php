<?php

namespace App\InventoryBundle\Tests\Functional\Service;

use App\InventoryBundle\Message\StockOutMessage;
use App\InventoryBundle\MessageHandler\StockOutMessageHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\RawMessage;
use Symfony\Component\Mime\Address;

class StockOutMessageHandlerTest extends TestCase
{
    public function testInvoke(): void
    {
        $mailerMock = $this->createMock(MailerInterface::class);

        $mailerMock->expects($this->once())
            ->method('send')
            ->with($this->isInstanceOf(Email::class));

        $handler = new StockOutMessageHandler($mailerMock);

        $message = new StockOutMessage('TESTSKU123', 'TESTBRANCH123');

        $handler($message);
    }
}
