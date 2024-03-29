<?php

namespace App\InventoryBundle\Tests\Functional\Service;

use App\InventoryBundle\Message\StockOutMessage;
use App\InventoryBundle\MessageHandler\StockOutMessageHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\RawMessage;
use Symfony\Component\Mime\Address;

/**
 * Functional tests for the StockOutMessageHandler class.
 *
 * Tests the behavior of the StockOutMessageHandler class by simulating the invocation
 * of the message handler and asserting that the email sending functionality is called
 * with the correct parameters.
 */
class StockOutMessageHandlerTest extends TestCase
{
    /**
     * Tests the invocation of the message handler.
     *
     * This method creates a mock mailer service and expects the send method to be
     * called once with an instance of Email. It then invokes the StockOutMessageHandler
     * with a test StockOutMessage object and asserts that the mailer's send method
     * is called with the expected parameters.
     * @throws TransportExceptionInterface
     */
    public function testInvoke(): void
    {
        // Create a mock mailer service
        $mailerMock = $this->createMock(MailerInterface::class);

        // Expect the send method to be called once with an instance of Email
        $mailerMock->expects($this->once())
            ->method('send')
            ->with($this->isInstanceOf(Email::class));

        // Create an instance of StockOutMessageHandler with the mock mailer
        $handler = new StockOutMessageHandler($mailerMock);

        // Create a test StockOutMessage object
        $message = new StockOutMessage('TESTSKU123', 'TESTBRANCH123');

        // Invoke the handler with the test message
        $handler($message);
    }
}
