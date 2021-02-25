<?php

namespace App\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractFOSRestController
{
    /**
     * @Rest\Get("/users")
     */
    public function listUsers(): Response
    {
        $data = ['temp' => 'list users'];
        $view = $this->view($data, 200);
        $view->setFormat('json');

        return $this->handleView($view);
    }
}
