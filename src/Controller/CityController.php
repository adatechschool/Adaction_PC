<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\DatabaseService;

class CityController extends AbstractController{

    #[Route('/cities', methods: ['GET'])]
    public function getAll(DatabaseService $db): Response
    {
        $cities = $db->query("SELECT * FROM cities");
        return $this->json($cities);
    }

    #[Route('/cities/{id}', methods: ['GET'])]
    public function getOne(DatabaseService $db, int $id): Response
    {
        $city = $db->query("SELECT * FROM cities WHERE id = :id", ['id' => $id]);
        return $this->json($city[0] ?? []);
    }

    #[Route('/cities', methods: ['POST'])]
    public function create(DatabaseService $db, Request $request): Response
{
    $data = json_decode($request->getContent(), true);

    if (!isset($data['name'], $data['latitude'], $data['longitude'])) {
        return $this->json(['error' => 'Champs requis manquants'], 400);
    }

    $db->execute(
        "INSERT INTO cities 
        (name, latitude, longitude) 
        VALUES (:name, :latitude, :longitude)",
        [
            'name' => $data['name'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
        ]
    );

    return $this->json(['message' => 'city created'], Response::HTTP_CREATED);
}


    #[Route('/cities/{id}', methods: ['PUT', 'PATCH'])]
    public function update(DatabaseService $db, Request $request, int $id): Response
    {
        $data = json_decode($request->getContent(), true);

        $db->execute(
            "UPDATE cities SET name = :name, latitude = :latitude, longitude = :longitude WHERE id = :id",
            [
            'name' => $data['name'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'id' => $id
            ]
        );

        return $this->json(['message' => 'City updated']);
    }

    #[Route('/cities/{id}', methods: ['DELETE'])]
    public function delete(DatabaseService $db, int $id): Response
    {
        $db->execute("DELETE FROM cities WHERE id = :id", ['id' => $id]);

        return $this->json(['message' => 'City deleted']);
    }
}