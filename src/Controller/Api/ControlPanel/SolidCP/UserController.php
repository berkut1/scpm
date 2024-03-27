<?php
declare(strict_types=1);

namespace App\Controller\Api\ControlPanel\SolidCP;

use App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\User\Check as SOAPUserCheck;
use App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\User\Create as SOAPUserCreate;
use App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\User\Edit as SOAPUserEdit;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class UserController extends AbstractController
{
    public function __construct(private readonly SerializerInterface $serializer, private readonly ValidatorInterface $validator) {}

    /**
     * @OA\Post(
     *     path="/solidCP/users",
     *     tags={"SolidCP User"},
     *     description="Manually create a SolidCP user. Not required if you are using All in One User/Package/Vps",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             required={"username", "email", "password"},
     *             @OA\Property(property="username", type="string"),
     *             @OA\Property(property="first_name", type="string", description="if not selected, then use the username"),
     *             @OA\Property(property="last_name", type="string", description="if not selected, then use the username"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string"),
     *             @OA\Property(property="id_enterprise_dispatcher", type="integer", description="if not selected, then use the default one"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Success response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="user_id", type="integer"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Errors",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Error",
     *         @OA\JsonContent(ref="#/components/schemas/SimpleError")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="InternalError",
     *         @OA\JsonContent(ref="#/components/schemas/InternalError")
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    #[Route('/solidCP/users', name: 'apiUser.create', methods: ['POST'])]
    public function create(Request $request, SOAPUserCreate\Handler $handler): Response
    {
        /** @var SOAPUserCreate\Command $command */
        $command = $this->serializer->deserialize($request->getContent(), SOAPUserCreate\Command::class, 'json');

        $violations = $this->validator->validate($command);
        if (\count($violations)) {
            $json = $this->serializer->serialize($violations, 'json');
            return new JsonResponse($json, Response::HTTP_BAD_REQUEST, [], true);
        }

        $userId = $handler->handle($command); //catch exceptions from Events in DomainExceptionFormatter

        return $this->json(['user_id' => $userId], Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/solidCP/users/{username}/is-exists",
     *     tags={"SolidCP User"},
     *     description="Check if the user exists. Not required if you are using All in One User/Package/Vps",
     *     @OA\Parameter(
     *         in="path",
     *         name="username",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="id_enterprise_dispatcher", description="if not selected, the default is used. No need to choose if only one enterprise is used",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer"),
     *         style="form"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="is_user_exists", type="boolean"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Errors",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Error",
     *         @OA\JsonContent(ref="#/components/schemas/SimpleError")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="InternalError",
     *         @OA\JsonContent(ref="#/components/schemas/InternalError")
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    #[Route('/solidCP/users/{username}/is-exists', name: 'apiUser.isExists', methods: ['GET'])]
    public function isExists(string $username, Request $request, SOAPUserCheck\Handler $handler): Response
    {
        $command = SOAPUserCheck\Command::create($username, (int)$request->query->get('id_enterprise_dispatcher'));

        $violations = $this->validator->validate($command);
        if (\count($violations)) {
            $json = $this->serializer->serialize($violations, 'json');
            return new JsonResponse($json, Response::HTTP_BAD_REQUEST, [], true);
        }

        $isExists = $handler->handle($command); //catch exceptions from Events in DomainExceptionFormatter

        return $this->json(['is_user_exists' => $isExists], Response::HTTP_OK);
    }

    /**
     * @OA\Put(
     *     path="/solidCP/users/{username}/email",
     *     tags={"SolidCP User"},
     *     description="Change SolidCP user's email",
     *     @OA\Parameter(
     *         in="path",
     *         name="username",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             required={"new_email"},
     *             @OA\Property(property="new_email", type="string"),
     *             @OA\Property(property="id_enterprise_dispatcher", type="integer", description="if not selected, the default is used. No need to choose if only one enterprise is used"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Success response",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Errors",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Error",
     *         @OA\JsonContent(ref="#/components/schemas/SimpleError")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="InternalError",
     *         @OA\JsonContent(ref="#/components/schemas/InternalError")
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    #[Route('/solidCP/users/{username}/email', name: 'apiUser.changeEmail', methods: ['PUT'])]
    public function changeEmail(string $username, Request $request, SOAPUserEdit\Email\Handler $handler): Response
    {
        /** @var SOAPUserEdit\Email\Command $command */
        $command = $this->serializer->deserialize($request->getContent(), SOAPUserEdit\Email\Command::class, 'json');
        $command->username = $username;

        $violations = $this->validator->validate($command);
        if (\count($violations)) {
            $json = $this->serializer->serialize($violations, 'json');
            return new JsonResponse($json, Response::HTTP_BAD_REQUEST, [], true);
        }

        $handler->handle($command); //catch exceptions from Events in DomainExceptionFormatter

        return $this->json([], Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\Put(
     *     path="/solidCP/users/{username}/password",
     *     tags={"SolidCP User"},
     *     description="Change SolidCP User password",
     *     @OA\Parameter(
     *         in="path",
     *         name="username",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             required={"new_password"},
     *             @OA\Property(property="new_password", type="string"),
     *             @OA\Property(property="id_enterprise_dispatcher", type="integer", description="if not selected, the default is used. No need to choose if only one enterprise is used"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Success response",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Errors",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Error",
     *         @OA\JsonContent(ref="#/components/schemas/SimpleError")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="InternalError",
     *         @OA\JsonContent(ref="#/components/schemas/InternalError")
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    #[Route('/solidCP/users/{username}/password', name: 'apiUser.changePassword', methods: ['PUT'])]
    public function changePassword(string $username, Request $request, SOAPUserEdit\Password\Handler $handler): Response
    {
        /** @var SOAPUserEdit\Password\Command $command */
        $command = $this->serializer->deserialize($request->getContent(), SOAPUserEdit\Password\Command::class, 'json');
        $command->username = $username;

        $violations = $this->validator->validate($command);
        if (\count($violations)) {
            $json = $this->serializer->serialize($violations, 'json');
            return new JsonResponse($json, Response::HTTP_BAD_REQUEST, [], true);
        }

        $handler->handle($command); //catch exceptions from Events in DomainExceptionFormatter

        return $this->json([], Response::HTTP_NO_CONTENT);
    }
}