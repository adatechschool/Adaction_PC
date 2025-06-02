<?php

namespace App\Controller;

use App\Service\DatabaseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TypesController extends AbstractController
{
    #[Route('/waste_types', methods: ['GET'])]
    public function getAll(DatabaseService $db): Response
    {
        $waste_types = $db->query("SELECT * FROM waste_types");
        return $this->json($waste_types);
    }

    #[Route('/waste_types/{id}', methods: ['GET'])]
    public function getOne(DatabaseService $db, int $id): Response
    {
        $waste_types = $db->query("SELECT * FROM waste_types WHERE id = :id", ['id'=> $id]);
        return $this->json($waste_types[0] ?? []);
    }

    #[Route('/waste_types', methods: ['POST'])]
    public function create(DatabaseService $db, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $db->execute(
            "INSERT INTO waste_types (value, label) VALUES (:value, :label)",

            [
                'value' => $data['value'],
                'label' => $data['label'],
            ]
        );

        return $this->json(['message' => 'Waste type created'], Response::HTTP_CREATED);
    }

    #[Route('/waste_types/{id}', methods: ['DELETE'])]
    public function delete(DatabaseService $db, int $id): Response
    {
        $db->execute(
            "DELETE FROM waste_types WHERE 
                id = :id", 
                ['id' => $id]);

        return $this->json(['message' => 'Waste type deleted']);
    }

    #[Route('/waste_types/{id}', methods: ['PUT'])]
public function update(DatabaseService $db, Request $request, int $id): Response
{
    $data = json_decode($request->getContent(), true);

    $db->execute(
        "UPDATE waste_types SET 
            value = :value,
            label = :label,
         WHERE id = :id",
        [
            'value' => $data['value'],
            'label' => $data['label'],
        ]
    );

    return $this->json(['message' => 'Waste type updated']);
}
}
