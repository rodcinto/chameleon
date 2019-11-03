<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends AbstractController
{
    public function index()
    {
        return new Response(
            'Default controller',
            Response::HTTP_OK,
            ['content-type' => 'text/html']
        );
    }
}
