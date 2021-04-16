<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\PaginatedRepresentation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Product;

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
     * @Rest\Get("/products", name="list_products")
     *
     * @QueryParam(name="page", requirements="\d+", default="1")
     * @QueryParam(name="limit", requirements="\d+", default="10")
     *
     * @OA\Response(
     *    response=200,
     *     description="Lists all products",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Product::class, groups={"list"}))
     *     )
     * )
     * @OA\Response(
     *     response=401,
     *     description="Unauthorized"
     * )
     * @OA\Parameter(
     *     name="limit",
     *     in="query",
     *     description="The maximum number of products displayed per page",
     *     @OA\Schema(type="int")
     * )
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="The page number",
     *     @OA\Schema(type="int")
     * )
     * @OA\Tag(name="Products")
     * @Security(name="Bearer")
     */
    public function listProducts(ParamFetcher $paramFetcher)
    {
        $limit = $paramFetcher->get('limit');
        $page = $paramFetcher->get('page');
        $products = $this->productRepository->getProducts($limit, $page);
        $collection = new CollectionRepresentation($products);
        $pages = (int)ceil($products->count() / $limit);
        $paginated = new PaginatedRepresentation(
            $collection,
            'list_products',
            array(),
            $page,
            $limit,
            $pages,
            'page',
            'limit'
        );

        $view = $this->view($paginated, 200);
        $view->getContext()->setGroups(['Default', 'items' => ['list']]);
        $view->setFormat('json');
        return $this->handleView($view);
    }

    /**
     * @Rest\Get("/products/{id}")
     *
     * @OA\Response(
     *     response=200,
     *     description="Describe a product in full detail",
     *     @Model(type=Product::class, groups={"describe"})
     * )
     * @OA\Response(
     *     response=401,
     *     description="Unauthorized"
     * )
     * @OA\Tag(name="Products")
     * @Security(name="Bearer")
     */
    public function describeProduct(int $id): Response
    {
        $product = $this->productRepository->find($id);
        if(is_null($product)){
            $view = $this->view(null, 404);
            $view->setFormat('json');
            return $this->handleView($view);
        }
        $view = $this->view($product, 200);
        $context = $view->getContext();
        $context->setGroups(['describe']);
        $view->setFormat('json');
        return $this->handleView($view);
    }
}
