<?php

namespace App\Controller;

use FOS\RestBundle\Context\Context;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractFOSRestController
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;


    public function __construct(ProductRepository $productRepository, EntityManagerInterface $entityManager)
    {
        $this->productRepository = $productRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Rest\Get("/products")
     */
    public function listProducts(): Response
    {
        $data = $this->productRepository->findAll();
        $view = $this->view($data, 200);
        $context = new Context();
        $context->setGroups(['list']);
        $view->setContext($context);
        $view->setFormat('json');
        return $this->handleView($view);
    }

    /**
     * @Rest\Get("/products/{id}")
     */
    public function describeProduct(int $id): Response
    {
        $user = $this->productRepository->find($id);
        $view = $this->view($user, 200);
        $context = new Context();
        $context->setGroups(['describe']);
        $view->setContext($context);
        $view->setFormat('json');
        return $this->handleView($view);
    }
}
