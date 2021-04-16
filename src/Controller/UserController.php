<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\PaginatedRepresentation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractFOSRestController
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Rest\Get("/users", name="list_users")
     * @QueryParam(name="page", requirements="\d+", default="1")
     * @QueryParam(name="limit", requirements="\d+", default="10")
     *
     * @OA\Response(
     *     response=200,
     *     description="Lists all users linked to logged client",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"list"}))
     *     )
     * )
     *
     * @OA\Response(
     *     response=401,
     *     description="Unauthorized"
     * )
     * @OA\Parameter(
     *     name="limit",
     *     in="query",
     *     description="The maximum number of users displayed per page",
     *     @OA\Schema(type="int")
     * )
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="The page number",
     *     @OA\Schema(type="int")
     * )
     * @OA\Tag(name="Users")
     * @Security(name="Bearer")
     */
    public function listUsers(ParamFetcher $paramFetcher)
    {
        $limit = $paramFetcher->get('limit');
        $page = $paramFetcher->get('page');
        $client = $this->getUser();
        $users = $this->userRepository->getUsers($client, $limit, $page);
        $collection = new CollectionRepresentation($users);
        $pages = (int)ceil($users->count() / $limit);
        $paginated = new PaginatedRepresentation(
            $collection,
            'list_users',
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
     * @Rest\Get("/users/{id}")
     *
     * @OA\Response(
     *     response=200,
     *     description="Give full detail of specified user",
     *     @Model(type=User::class, groups={"describe"})
     * )
     * @OA\Response(
     *     response=401,
     *     description="Unauthorized"
     * )
     * @OA\Tag(name="Users")
     * @Security(name="Bearer")
     */
    public function describeUser(int $id): Response
    {
        $user = $this->userRepository->find($id);
        $loggedUser = $this->getUser();
        if(is_null($user) || $user->getClient() !== $loggedUser){
            $view = $this->view(null, 404);
            $view->setFormat('json');
            return $this->handleView($view);
        }
        $view = $this->view($user, 200);
        $context = new Context();
        $context->setGroups(['describe']);
        $view->setContext($context);
        $view->setFormat('json');
        return $this->handleView($view);
    }

    /**
     * @Rest\Post("/users")
     * @ParamConverter("user", converter="fos_rest.request_body")
     *
     * @OA\RequestBody(
     *     @Model(type=User::class, groups={"describe"})
     * )
     * @OA\Response(
     *     response=201,
     *     description="Creates a new User for the logged client"
     * )
     * @OA\Response(
     *     response=401,
     *     description="Unauthorized"
     * )
     *
     * @OA\Tag(name="Users")
     * @Security(name="Bearer")
     */
    public function postUser(User $user): Response
    {
        $client = $this->getUser();
        $user->setClient($client);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $view = $this->view($user, 201);
        $view->setFormat('json');
        return $this->handleView($view);
    }

    /**
     * @Rest\Delete("/users/{id}")
     *
     * @OA\Response(
     *     response=204,
     *     description="Delete the specified User"
     * )
     * @OA\Response(
     *     response=401,
     *     description="Unauthorized"
     * )
     * @OA\Tag(name="Users")
     * @Security(name="Bearer")
     */
    public function deleteUser(int $id): Response
    {
        $client = $this->getUser();
        $user = $this->userRepository->find($id);
        if(is_null($user)){
            $view = $this->view(null, 404);
            $view->setFormat('json');
            return $this->handleView($view);
        }
        if($user->getClient() !== $client){
            $view = $this->view(null, 403);
            $view->setFormat('json');
            return $this->handleView($view);
        }
        $this->entityManager->remove($user);
        $this->entityManager->flush();
        $view = $this->view(null, 204);
        $view->setFormat('json');
        return $this->handleView($view);
    }
}
