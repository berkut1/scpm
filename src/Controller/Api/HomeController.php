<?php
declare(strict_types=1);

namespace App\Controller\Api;

use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

///**
// * @OA\Info(
// *     version="1.0.0",
// *     title="SCPM API",
// *     description="HTTP JSON API",
// * ),
// * @OA\Server(
// *     url="/api"
// * ),
// * @OA\SecurityScheme(
// *     securityScheme="bearerAuth",
// *     in="header",
// *     name="bearerAuth",
// *     type="http",
// *     scheme="bearer",
// *     bearerFormat="JWT",
// * ),
// * @OA\Schema(
// *     schema="ErrorModel",
// *     type="object",
// *     @OA\Property(property="error", type="object", nullable=true,
// *         @OA\Property(property="code", type="integer"),
// *         @OA\Property(property="message", type="string"),
// *     ),
// *     @OA\Property(property="violations", type="array", nullable=true, @OA\Items(
// *         type="object",
// *         @OA\Property(property="propertyPath", type="string"),
// *         @OA\Property(property="title", type="string"),
// *     )),
// * ),
// * @OA\Schema(
// *     schema="SimpleError",
// *     type="object",
// *        @OA\Property(property="error", type="object", nullable=true,
// *            @OA\Property(property="code", type="integer"),
// *            @OA\Property(property="message", type="string"),
// *      ),
// * ),
// * @OA\Schema(
// *     schema="InternalError",
// *     type="object",
// *        @OA\Property(property="title", type="string"),
// *        @OA\Property(property="code", type="integer"),
// *        @OA\Property(property="detail", type="string"),
// * )
// */
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
//    /**
//     * @OA\Get(
//     *     path="/",
//     *     tags={"API"},
//     *     description="API Home",
//     *     @OA\Response(
//     *         response="200",
//     *         description="Success response",
//     *         @OA\JsonContent(
//     *             type="object",
//     *             @OA\Property(property="name", type="string")
//     *         )
//     *     )
//     * )
//     */
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