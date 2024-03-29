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
     * @throws ToolsException
     */
    public function testSaveStockData(): void
    {
        $client = static::createClient();
        $entityManager = self::$container->get('doctrine.orm.entity_manager');

        if (!$entityManager instanceof \Doctrine\ORM\EntityManagerInterface) {
            throw new \LogicException('Entity manager not found');
        }

        $metadatas = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($entityManager);
        $schemaTool->dropDatabase();
        $schemaTool->createSchema($metadatas);

        $client->request('POST', '/stock/save', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'sku' => 'TESTSKU123',
            'branch' => 'TESTBRANCH123',
            'stock' => 10
        ]));

        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $this->assertStringContainsString('Stock data saved successfully', $client->getResponse()->getContent());
    }
}
