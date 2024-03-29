<?php
namespace App\InventoryBundle\Tests\Functional\Controller;

use Doctrine\ORM\Tools\ToolsException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Functional tests for the StockController::save method.
 *
 * Tests the endpoint's response and behavior by simulating a client request to
 * ensure the stock data is saved correctly and responds with the expected HTTP status.
 */
class StockControllerSaveTest extends WebTestCase
{
    /**
     * Tests the save action for stock data.
     *
     * This method tests the behavior of the stock save endpoint by sending a POST request
     * with JSON data representing stock information. It asserts that the endpoint responds
     * with the expected HTTP status code (201 Created) and returns a JSON response with
     * a success message.
     *
     * @throws ToolsException If there is an error with the database tools.
     */
    public function testSaveStockData(): void
    {
        $client = static::createClient();
        $entityManager = self::$container->get('doctrine.orm.entity_manager');

        if (!$entityManager instanceof \Doctrine\ORM\EntityManagerInterface) {
            throw new \LogicException('Entity manager not found');
        }

        // Create schema for testing
        $metadatas = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($entityManager);
        $schemaTool->dropDatabase();
        $schemaTool->createSchema($metadatas);

        // Send a POST request to save stock data
        $client->request('POST', '/stock/save', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'sku' => 'TESTSKU123',
            'branch' => 'TESTBRANCH123',
            'stock' => 10
        ]));

        // Assert HTTP status code is 201 Created
        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
        // Assert response content is JSON
        $this->assertJson($client->getResponse()->getContent());
        // Assert response contains success message
        $this->assertStringContainsString('Stock data saved successfully', $client->getResponse()->getContent());
    }
}
