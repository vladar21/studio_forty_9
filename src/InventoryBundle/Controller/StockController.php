<?php

namespace App\InventoryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\InventoryBundle\Service\StockService;

class StockController extends AbstractController
{
    private StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * @Route("/stocks", name="stock_index")
     */
    public function index(): Response
    {
        $stocks = $this->stockService->getAllStocks();

        return $this->render('@InventoryBundle/stock/index.html.twig', [
            'stocks' => $stocks,
        ]);
    }

    /**
     * Saves stock data from a POST request.
     *
     * @Route("/stock/save", name="stock_save", methods={"POST"})
     */
    public function save(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $response = $this->stockService->saveStockData($data);

        return new Response($response['message'], $response['status']);
    }

}
