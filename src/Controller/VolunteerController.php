<?php

namespace App\Controller;

use App\Service\DatabaseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VolunteerController extends AbstractController
{
    #[Route('/volunteers', methods: ['GET'])]
    public function getAll(DatabaseService $db): Response
    {
        $volunteers = $db->query("SELECT * FROM volunteers");
        return $this->json($volunteers);
    }

    #[Route('/volunteers/index', methods:['GET'])]
    public function getIndex(DatabaseService $db): Response
    {
        $volunteersIndex = $db -> query("SELECT volunteers.firstname, volunteers.lastname, cities.name 
        FROM volunteers INNER JOIN cities on volunteers.city_id = cities.id");
        return $this-> json($volunteersIndex);
    }

    #[Route('/volunteer/{email}', methods: ['GET'])]
    public function getOne(DatabaseService $db, string $email): Response
    {
        $volunteer = $db->query("SELECT * FROM volunteers WHERE email = :email", ['email' => $email]);
        return $this->json($volunteer[0] ?? []);
    }

/*     #[Route('/volunteer/{id}', methods: ['GET'])]
    public function getOne(DatabaseService $db, int $id): Response
    {
        $volunteer = $db->query("SELECT * FROM volunteers WHERE id = :id", ['id' => $id]);
        return $this->json($volunteer[0] ?? []);
    } */

    #[Route('/volunteer', methods: ['POST'])]
    public function create(DatabaseService $db, Request $request): Response
{
    $data = json_decode($request->getContent(), true);

    if (!isset($data['firstname'], $data['lastname'], $data['email'], $data['password'], $data['city_id'])) {
        return $this->json(['error' => 'Champs requis manquants'], 400);
    }

    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    return $this->json(['error' => 'Email invalide'], 400);
    }

    // Hashage du mot de passe
    $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

    $db->execute(
        "INSERT INTO volunteers 
        (firstname, lastname, email, password, Is_admin, city_id, created_at, updated_at) 
        VALUES (:firstname, :lastname, :email, :password, :Is_admin, :city_id, NOW(), NOW())",
        [
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'email' => $data['email'],
            'password' => $hashedPassword,
            'Is_admin' => $data['Is_admin'],
            'city_id' => $data['city_id'],
        ]
    );

    return $this->json(['message' => 'Volunteer created'], Response::HTTP_CREATED);
}


    #[Route('/volunteer/{id}', methods: ['PUT', 'PATCH'])]
    public function update(DatabaseService $db, Request $request, int $id): Response
    {
        $data = json_decode($request->getContent(), true);

        $db->execute(
            "UPDATE volunteers SET firstname = :firstname, lastname = :lastname, email = :email, Is_admin = :Is_admin, city_id = :city_id, updated_at= NOW() WHERE id = :id",
            [
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'email' => $data['email'],
            'Is_admin' => $data['Is_admin'],
            'city_id' => $data['city_id'],
            'id' => $id
            ]
        );

        return $this->json(['message' => 'Volunteer updated']);
    }

    #[Route('/volunteer/{id}', methods: ['DELETE'])]
    public function delete(DatabaseService $db, int $id): Response
    {
        $db->execute("DELETE FROM volunteers WHERE id = :id", ['id' => $id]);

        return $this->json(['message' => 'Volunteer deleted']);
    }
}
