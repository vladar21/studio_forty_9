<?php

namespace App\InventoryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\InventoryBundle\Entity\Stock;

class StockController extends AbstractController
{
    /**
     * @Route("/stocks", name="stock_index")
     */
    public function index(): Response
    {
        $stocks = $this->getDoctrine()->getRepository(Stock::class)->findAll();

        return $this->render('@InventoryBundle/stock/index.html.twig', [
            'stocks' => $stocks,
        ]);
    }
}
