<?php

namespace App\Controller;

use FOS\RestBundle\Context\Context;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Entity\User;
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
     * @Rest\Get("/users")
     */
    public function listUsers(): Response
    {
        //TODO: Filter by logged client once Auth is implemented
        $data = $this->userRepository->findAll();
        $view = $this->view($data, 200);
        $context = new Context();
        $context->setGroups(['list']);
        $view->setContext($context);
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
