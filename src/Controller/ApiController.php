<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiController extends AbstractController
{
    public function index()
    {
        return $this->json([
            'message' => 'This is the API controller',
            'path' => 'src/Controller/ApiController.php',
        ]);
    }
}
