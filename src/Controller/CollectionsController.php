<?php

namespace App\Controller;

use App\Service\DatabaseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CollectionsController extends AbstractController
{
    #[Route('/waste_collections', methods: ['GET'])]
    public function getAll(DatabaseService $db): Response
    {
        $waste_collections = $db->query("SELECT * FROM waste_collections");
        return $this->json($waste_collections);
    }

    #[Route('/waste_collections/{id}', methods: ['GET'])]
    public function getOne(DatabaseService $db, int $id): Response
    {
        $waste_collections = $db->query("SELECT * FROM waste_collections WHERE id = :id", ['id'=> $id]);
        return $this->json($waste_collections[0] ?? []);
    }

    #[Route('/waste_collections', methods: ['POST'])]
    public function create(DatabaseService $db, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $db->execute(
            'INSERT INTO waste_collections (volunteer_id, wasteType_id, quantity, city_id, collectionDate) VALUES (:volunteer_id, :wasteType_id, :quantity, :city_id, :collectionDate)',

            [
                'volunteer_id' => $data['volunteer_id'],
                'wasteType_id' => $data['wasteType_id'],
                'quantity' => $data['quantity'],
                'city_id' => $data['city_id'],
                'collectionDate' => $data['collectionDate']
            ]
        );

        return $this->json(['message' => 'Collection created'], Response::HTTP_CREATED);
    }

    #[Route('/waste_collections/{id}', methods: ['DELETE'])]
    public function delete(DatabaseService $db, int $id): Response
    {
        $db->execute("DELETE FROM waste_collections WHERE id = :id", ['id' => $id]);

        return $this->json(['message' => 'Collection deleted']);
    }

    #[Route('/waste_collections/{id}', methods: ['PUT'])]
public function update(DatabaseService $db, Request $request, int $id): Response
{
    $data = json_decode($request->getContent(), true);

    $db->execute(
        "UPDATE waste_collections SET 
            volunteer_id = :volunteer_id,
            wasteType_id = :wasteType_id,
            quantity = :quantity,
            city_id = :city_id,
            collectionDate = :collectionDate
         WHERE id = :id",
        [
            'volunteer_id' => $data['volunteer_id'],
            'wasteType_id' => $data['wasteType_id'],
            'quantity' => $data['quantity'],
            'city_id' => $data['city_id'],
            'collectionDate' => $data['collectionDate'],
            'id' => $id
        ]
    );

    return $this->json(['message' => 'Collection updated']);
}
}

