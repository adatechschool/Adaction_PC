<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\DatabaseService;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(DatabaseService $db, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Vérifie que les champs sont présents
        if (!isset($data['email'], $data['password'])) {
            return new JsonResponse(['error' => 'Champs manquants'], 400);
        }

        // Récupère l'utilisateur par email
        $user = $db->query(
            "SELECT * FROM volunteers WHERE email = :email",
            ['email' => $data['email']]
        );

        // Si aucun utilisateur trouvé
        if (!$user) {
            return new JsonResponse(['error' => 'Email inconnu'], 401);
        }

        $user = $user[0];

        // Vérifie le mot de passe
        if (!password_verify($data['password'], $user['password'])) {
            return new JsonResponse(['error' => 'Mot de passe incorrect'], 401);
        }

        // Ne pas renvoyer le mot de passe hashé au front
        unset($user['password']);

        // Retourne les infos utilisateur (ou un token si tu veux aller plus loin)
        return new JsonResponse([
            'message' => 'Connexion réussie',
            'user' => $user
        ], 200);
    }
}