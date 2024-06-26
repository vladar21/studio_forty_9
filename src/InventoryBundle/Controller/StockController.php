<?php

namespace App\InventoryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\InventoryBundle\Service\StockService;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Controller for handling stock-related actions.
 */
class StockController extends AbstractController
{
    private StockService $stockService;

    /**
     * StockController constructor.
     *
     * @param StockService $stockService The stock service.
     */
    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Renders the index page with stock data.
     *
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
     * @param Request $request The HTTP request object.
     * @return JsonResponse The JSON response.
     */
    public function save(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $response = $this->stockService->saveStockData($data);

        return new JsonResponse(['message' => $response['message']], $response['status']);
    }

}
