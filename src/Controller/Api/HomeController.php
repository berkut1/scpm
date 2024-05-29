<?php
declare(strict_types=1);

namespace App\Controller\Api;

use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Info(version: '1.0.0', description: 'HTTP JSON API', title: 'SCPM API')]
#[OA\Server(url: '/api')]
#[OA\SecurityScheme(securityScheme: 'bearerAuth', type: 'http', name: 'bearerAuth', in: 'header', bearerFormat: 'JWT', scheme: 'bearer')]
#[OA\Schema(schema: 'ErrorModel',
    properties: [
        new OA\Property(property: 'error', properties: [
            new OA\Property(property: 'code', type: 'integer'),
            new OA\Property(property: 'message', type: 'string'),
        ], type: 'object', nullable: true),
        new OA\Property(property: 'violations', type: 'array', items: new OA\Items(properties: [
            new OA\Property(property: 'propertyPath', type: 'string'),
            new OA\Property(property: 'title', type: 'string'),
        ], type: 'object'), nullable: true),
    ],
    type: 'object')]
#[OA\Schema(schema: 'SimpleError',
    properties: [
        new OA\Property(property: 'error', properties: [
            new OA\Property(property: 'code', type: 'integer'),
            new OA\Property(property: 'message', type: 'string'),
        ], type: 'object', nullable: true),
    ], type: 'object')]
#[OA\Schema(schema: 'InternalError',
    properties: [
        new OA\Property(property: 'title', type: 'string'),
        new OA\Property(property: 'code', type: 'integer'),
        new OA\Property(property: 'detail', type: 'string'),
    ], type: 'object')]
final class HomeController extends AbstractController
{
    #[OA\Get(path: '/', description: 'API Home', tags: ['API'],
        responses: [
            new OA\Response(response: 200, description: 'Success response',
                content: [
                    new OA\JsonContent(properties: [
                        new OA\Property(property: 'name', type: 'string'),
                    ], type: 'object'),
                ]),
        ])]
    #[Route('/', name: 'home', methods: ['GET'])]
    public function index(): Response
    {
        return $this->json([
            'name' => 'JSON API v1',
        ]);
    }
}