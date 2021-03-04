<?php

namespace App\Security;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Firebase\JWT\JWT;
use App\Entity\Client;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class JWTAuthenticator extends AbstractGuardAuthenticator
{
    private $em;
    private $params;

    public function __construct(EntityManagerInterface $em, ContainerBagInterface $params)
    {
        $this->em = $em;
        $this->params = $params;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = [
            'message' => 'Authentication Required'
        ];
        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function supports(Request $request)
    {
        if(!$request->headers->has('Authorization')){
            throw new AuthenticationException('Authentication Required');
        }
        return true;
    }

    public function getCredentials(Request $request)
    {
        return $request->headers->get('Authorization');
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        try{
            $credentials = str_replace('Bearer ', '', $credentials);
            $jwt = (array) JWT::decode(
                $credentials,
                $this->params->get('jwt_secret'),
                ['HS256']
            );
            $client = $this->em->getRepository(Client::class)
                ->findOneBy([
                                'name' => $jwt['client'],
                            ]);
            if($client === NULL){
                throw new AuthenticationException('No client linked to this token');
            }
            return $client;
        }catch (\Exception $e){
            throw new AuthenticationException('Invalid token');
        }

    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse([
                                    'message' => $exception->getMessage()
                                ], Response::HTTP_UNAUTHORIZED);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        return;
    }

    public function supportsRememberMe()
    {
        return false;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }
}
