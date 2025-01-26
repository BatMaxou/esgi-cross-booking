<?php

namespace App\Controller\Api;

use App\Repository\CrossingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route(path: '/api', name: 'api_')]
class ApiController extends AbstractController
{
    public function __construct()
    {
    }

    #[Route(path: '/crossings', name: 'crossings')]
    public function crossings(
        CrossingRepository $crossingRepository,
        SerializerInterface $serializer,
    ): JsonResponse {
        $crossings = $crossingRepository->findAll();

        return new JsonResponse(
            $serializer->serialize($crossings, 'json', ['groups' => 'api:crossings']),
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route(path: '/crossings/{id}', name: 'crossing')]
    public function crossing(
        string $id,
        CrossingRepository $crossingRepository,
        SerializerInterface $serializer,
    ): JsonResponse {
        $crossing = $crossingRepository->findOneBy(['id' => $id]);

        if (!$crossing) {
            return new JsonResponse(
                null,
                Response::HTTP_NOT_FOUND,
            );
        }

        return new JsonResponse(
            $serializer->serialize($crossing, 'json', ['groups' => 'api:crossing']),
            Response::HTTP_OK,
            [],
            true
        );
    }
}
