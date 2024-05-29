<?php
declare(strict_types=1);

namespace App\Controller\Api\ControlPanel\SolidCP;

use App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\User\Check as SOAPUserCheck;
use App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\User\Create as SOAPUserCreate;
use App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\User\Edit as SOAPUserEdit;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class UserController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface  $validator
    ) {}

    #[OA\Post(
        path: '/solidCP/users',
        description: 'Manually create a SolidCP user. Not required if you are using All in One User/Package/Vps',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                required: ['username', 'email', 'password'],
                properties: [
                    new OA\Property(property: 'username', type: 'string'),
                    new OA\Property(property: 'first_name', description: 'if not selected, then use the username', type: 'string'),
                    new OA\Property(property: 'last_name', description: 'if not selected, then use the username', type: 'string'),
                    new OA\Property(property: 'email', type: 'string'), new OA\Property(property: 'password', type: 'string'),
                    new OA\Property(property: 'id_enterprise_dispatcher', description: 'if not selected, then use the default one', type: 'integer')],
                type: 'object')),
        tags: ['SolidCP User'],
        responses: [
            new OA\Response(response: 201, description: 'Success response', content: new OA\JsonContent(
                properties: [new OA\Property(property: 'user_id', type: 'integer')],
                type: 'object')
            ),
            new OA\Response(
                response: 400, description: 'Errors', content: new OA\JsonContent(ref: '#/components/schemas/ErrorModel')
            ),
            new OA\Response(
                response: 401, description: 'Error', content: new OA\JsonContent(ref: '#/components/schemas/SimpleError')
            ),
            new OA\Response(
                response: 500, description: 'InternalError', content: new OA\JsonContent(ref: '#/components/schemas/InternalError')
            ),
        ]
    )]
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

    #[OA\Get(
        path: '/solidCP/users/{username}/is-exists',
        description: 'Check if the user exists. Not required if you are using All in One User/Package/Vps',
        security: [['bearerAuth' => []]],
        tags: ['SolidCP User'],
        parameters: [
            new OA\Parameter(
                name: 'username',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string'),
            ),
            new OA\Parameter(
                name: 'id_enterprise_dispatcher',
                description: 'if not selected, the default is used. No need to choose if only one enterprise is used',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer'), style: 'form'),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success response',
                content: new OA\JsonContent(
                    properties: [new OA\Property(property: 'is_user_exists', type: 'boolean')],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 400, description: 'Errors', content: new OA\JsonContent(ref: '#/components/schemas/ErrorModel')
            ),
            new OA\Response(
                response: 401, description: 'Error', content: new OA\JsonContent(ref: '#/components/schemas/SimpleError')
            ),
            new OA\Response(
                response: 500, description: 'InternalError', content: new OA\JsonContent(ref: '#/components/schemas/InternalError')
            ),
        ]
    )]
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

    #[OA\Put(
        path: '/solidCP/users/{username}/email',
        description: "Change SolidCP user's email",
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                required: ['new_email'],
                properties: [
                    new OA\Property(property: 'new_email', type: 'string'),
                    new OA\Property(property: 'id_enterprise_dispatcher', description: 'if not selected, the default is used. No need to choose if only one enterprise is used', type: 'integer'),
                ],
                type: 'object')),
        tags: ['SolidCP User'],
        parameters: [
            new OA\Parameter(name: 'username', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(
                response: 204, description: 'Success response'
            ),
            new OA\Response(
                response: 400, description: 'Errors', content: new OA\JsonContent(ref: '#/components/schemas/ErrorModel')
            ),
            new OA\Response(
                response: 401, description: 'Error', content: new OA\JsonContent(ref: '#/components/schemas/SimpleError')
            ),
            new OA\Response(
                response: 500, description: 'InternalError', content: new OA\JsonContent(ref: '#/components/schemas/InternalError')
            ),
        ]
    )]
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

    #[OA\Put(
        path: '/solidCP/users/{username}/password',
        description: 'Change SolidCP User password',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                required: ['new_password'],
                properties: [
                    new OA\Property(property: 'new_password', type: 'string'),
                    new OA\Property(property: 'id_enterprise_dispatcher', description: 'if not selected, the default is used. No need to choose if only one enterprise is used', type: 'integer'),
                ],
                type: 'object'
            )
        ),
        tags: ['SolidCP User'],
        parameters: [
            new OA\Parameter(name: 'username', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(
                response: 204, description: 'Success response'
            ),
            new OA\Response(
                response: 400, description: 'Errors', content: new OA\JsonContent(ref: '#/components/schemas/ErrorModel')
            ),
            new OA\Response(
                response: 401, description: 'Error', content: new OA\JsonContent(ref: '#/components/schemas/SimpleError')
            ),
            new OA\Response(
                response: 500, description: 'InternalError', content: new OA\JsonContent(ref: '#/components/schemas/InternalError')
            ),
        ]
    )]
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