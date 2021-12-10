<?php
declare(strict_types=1);

namespace App\Controller\Api\Auth;

use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    /**
     * @OA\Post(
     *     path="/login/authentication_token",
     *     tags={"Get JWT toket to Login"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             required={"username", "password"},
     *             @OA\Property(property="username", type="string"),
     *             @OA\Property(property="password", type="string"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="token", type="string"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Error",
     *         @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="error", type="object", nullable=true,
     *                  @OA\Property(property="code", type="integer"),
     *                  @OA\Property(property="message", type="string"),
     *              ),
     *         ),
     *     ),
     * )
     */
    #[Route('/login/authentication_token', name: 'authentication_token', methods: ['POST'])]
    public function login_check(): Response
    {
        //jwt catch it - look in security.yaml
        //this method need only to generate API docs
        return new Response('', Response::HTTP_UNAUTHORIZED); // The security layer will intercept this request
    }
}