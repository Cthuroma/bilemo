<?php

namespace App\Controller;

use App\Entity\Client;
use App\Repository\ClientRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Firebase\JWT\JWT;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AuthController extends AbstractFOSRestController
{
    /**
     * @var ClientRepository
     */
    private $clientRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(
        ClientRepository $clientRepository,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $encoder
    ) {
        $this->clientRepository = $clientRepository;
        $this->entityManager = $entityManager;
        $this->encoder = $encoder;
    }

    /**
     * @Rest\Post ("/login")
     * @ParamConverter("client", converter="fos_rest.request_body")
     * @OA\RequestBody(
     *     @Model(type=Client::class, groups={"login"})
     * )
     * @OA\Response(
     *     response=200,
     *     description="Returns a Bearer Token for auth"
     * )
     * @OA\Tag(name="Auth")
     */
    public function login(Client $client)
    {
        $dbClient = $this->clientRepository->findOneByName($client->getName());

        if (!$dbClient || !$this->encoder->isPasswordValid($dbClient, $client->getPassword())) {
            $data = ['message' => 'Wrong credentials',];
            $view = $this->view($data, 401);
            $view->setFormat('json');
            return $this->handleView($view);
        }

        $payload = [
            "client" => $client->getName(),
            "exp" => (new DateTime())->modify("+5 hours")->getTimestamp(),
        ];

        $jwt = JWT::encode($payload, $this->getParameter('jwt_secret'));
        $data = ['message' => 'Successfully logged in', 'token' => $jwt, 'type' => 'Bearer'];
        $view = $this->view($data, 200);
        $view->setFormat('json');
        return $this->handleView($view);
    }
}
