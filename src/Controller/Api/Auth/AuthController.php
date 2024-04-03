<?php
declare(strict_types=1);

namespace App\Controller\Api\Auth;

use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AuthController extends AbstractController
{

    #[OA\Post(
        path: '/login/authentication_token',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                required: ['username', 'password'],
                properties: [
                    new OA\Property(property: 'username', type: 'string'),
                    new OA\Property(property: 'password', type: 'string'),
                ],
                type: 'object')),
        tags: ['Get JWT toket to Login'],
        responses: [
            new OA\Response(response: 200, description: 'Success response',
                content: new OA\JsonContent(
                    properties: [new OA\Property(property: 'token', type: 'string')],
                    type: 'object')),
            new OA\Response(response: 401, description: 'Error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', properties: [
                            new OA\Property(property: 'code', type: 'integer'),
                            new OA\Property(property: 'message', type: 'string'),
                        ], type: 'object', nullable: true),
                    ],
                    type: 'object')),
        ])
    ]
    #[Route('/login/authentication_token', name: 'authentication_token', methods: ['POST'])]
    public function login_check(): Response
    {
        //jwt catch it - look in security.yaml
        //this method need only to generate API docs
        return new Response('', Response::HTTP_UNAUTHORIZED); // The security layer will intercept this request
    }
}