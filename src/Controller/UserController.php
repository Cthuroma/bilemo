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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;

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
     *
     * @QueryParam(name="page", requirements="\d+", default="1")
     * @QueryParam(name="limit", requirements="\d+", default="10")
     */
    public function listUsers(ParamFetcher $paramFetcher): Response
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
        $view->setFormat('json');
        return $this->handleView($view);
    }

    /**
     * @Rest\Get("/users/{id}")
     */
    public function describeUser(int $id): Response
    {
        //TODO: Send 404 on found User when not linked to logged client once Auth is implemented
        $user = $this->userRepository->find($id);
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
     */
    public function postUser(User $user, ConstraintViolationListInterface $validationErrors): Response
    {
        //TODO: Validate data and inject logged client
        if (count($validationErrors) > 0) {
            $view = $this->view($validationErrors, 400);
            $view->setFormat('json');
            return $this->handleView($view);
        }
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $view = $this->view($user, 201);
        $view->setFormat('json');
        return $this->handleView($view);
    }

    /**
     * @Rest\Delete("/users/{id}")
     */
    public function deleteUser(int $id): Response
    {
        //TODO : Verify is logged client owns this user
        $user = $this->userRepository->find($id);
        $this->entityManager->remove($user);
        $this->entityManager->flush();
        $view = $this->view(null, 204);
        $view->setFormat('json');
        return $this->handleView($view);
    }
}
