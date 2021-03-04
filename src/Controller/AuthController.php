<?php

namespace App\Controller;

use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Entity\Client;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Firebase\JWT\JWT;

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

    public function __construct(ClientRepository $clientRepository, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder)
    {
        $this->clientRepository = $clientRepository;
        $this->entityManager = $entityManager;
        $this->encoder = $encoder;
    }

    /**
     * @Rest\Post ("/login")
     * @ParamConverter("client", converter="fos_rest.request_body")
     */
    public function login(Client $client)
    {
        $dbClient = $this->clientRepository->findOneByName($client->getName());

        if (!$dbClient || !$this->encoder->isPasswordValid($dbClient, $client->getPassword())) {
            return $this->json([
                                   'message' => 'Name or Password is wrong.',
                               ]);
        }
        $payload = [
            "client" => $client->getName(),
            "exp"  => (new \DateTime())->modify("+5 hours")->getTimestamp(),
        ];


        $jwt = JWT::encode($payload, $this->getParameter('jwt_secret'));
        return $this->json([
                               'message' => 'success!',
                               'token' => $jwt,
                               'type' => 'Bearer'
                           ]);
    }
}
