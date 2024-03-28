<?php

namespace App\InventoryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\InventoryBundle\Entity\Stock;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class StockController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/stocks", name="stock_index")
     */
    public function index(): Response
    {
        $stocks = $this->entityManager->getRepository(Stock::class)->findAll();

        return $this->render('@InventoryBundle/stock/index.html.twig', [
            'stocks' => $stocks,
        ]);
    }

    /**
     * Saves stock data from a POST request.
     *
     * @Route("/stock/save", name="stock_save", methods={"POST"})
     */
    public function save(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        $data = json_decode($request->getContent(), true);

        $stock = new Stock();
        $stock->setSku($data['sku']);
        $stock->setBranch($data['branch']);
        $stock->setStock($data['stock']);

        // Validate the stock object
        $errors = $validator->validate($stock);

        if (count($errors) > 0) {
            $errorsString = $this->serializeValidationErrors($errors);
            return new Response($errorsString, Response::HTTP_BAD_REQUEST);
        }

        $entityManager->persist($stock);
        $entityManager->flush();

        return new Response('Stock data saved successfully', Response::HTTP_CREATED);
    }

    /**
     * Serializes validation errors to a string.
     *
     * @param ConstraintViolationListInterface $errors Validation errors
     * @return string Serialized errors
     */
    private function serializeValidationErrors(ConstraintViolationListInterface $errors): string
    {
        $errorsString = '';

        foreach ($errors as $error) {
            $errorsString .= $error->getPropertyPath() . ': ' . $error->getMessage() . "\n";
        }

        return $errorsString;
    }
}
