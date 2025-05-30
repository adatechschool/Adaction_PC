<?php

namespace App\Controller;

use App\Service\DatabaseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/test', name: 'test')]
    public function index(DatabaseService $db): Response
    {
        $result = $db->query("SELECT * FROM volunteers");

        return $this->json($result);
    }
}