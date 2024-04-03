<?php
declare(strict_types=1);

namespace App\Controller\Api\ControlPanel\SolidCP;

use App\Model\ControlPanel\UseCase\Panel\SolidCP\EnterpriseDispatcher\IsEnable;
use App\ReadModel\ControlPanel\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcherFetcher;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class EnterpriseDispatchersController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface  $validator
    ) {}

    #[OA\Get(
        path: '/solidCP/enterprise-dispatchers',
        description: 'Get list of Enterprise Dispatchers, use only if you have more than one Enterprise Dispatchers',
        security: [['bearerAuth' => []]],
        tags: ['Enterprise Dispatchers'],
        responses: [
            new OA\Response(
                response: 200, description: 'Success response',
                content: new OA\JsonContent(description: 'return ids and names', type: 'array',
                    items: new OA\Items(properties: [new OA\Property(type: 'string')])
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
    #[Route('/solidCP/enterprise-dispatchers', name: 'apiEnterpriseDispatchers.allList', methods: ['GET'])]
    public function allList(EnterpriseDispatcherFetcher $enterpriseDispatcherFetcher): Response
    {
        $enterpriseDispatchers = $enterpriseDispatcherFetcher->allList();

        return $this->json([$enterpriseDispatchers], Response::HTTP_OK);
    }

    #[OA\Get(
        path: '/solidCP/enterprise-dispatchers/default/is-enable',
        description: 'Check if default Enterprise Dispatcher is manually disabled',
        security: [['bearerAuth' => []]],
        tags: ['Enterprise Dispatchers'],
        parameters: [
            new OA\Parameter(
                name: 'id_enterprise_dispatcher',
                description: 'if not pass value, it will use a default',
                in: 'query',
                required: false, schema: new OA\Schema(type: 'integer'),
                style: 'form'
            ),
        ],
        responses: [
            new OA\Response(
                response: 200, description: 'Success response',
                content: new OA\JsonContent(
                    properties: [new OA\Property(property: 'is_enable', type: 'boolean')],
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
        ],
    )]
    #[Route('/solidCP/enterprise-dispatchers/default/is-enable', name: 'apiEnterpriseDispatchers.isEnableDefault', methods: ['GET'])]
    public function isEnableDefault(IsEnable\Handler $handler): Response
    {
        $command = new IsEnable\Command();

        $violations = $this->validator->validate($command);
        if (\count($violations)) {
            $json = $this->serializer->serialize($violations, 'json');
            return new JsonResponse($json, Response::HTTP_BAD_REQUEST, [], true);
        }

        $isEnable = $handler->handle($command); //catch exceptions from Events in DomainExceptionFormatter

        return $this->json(['is_enable' => $isEnable], Response::HTTP_OK);
    }

    #[OA\Get(
        path: '/solidCP/enterprise-dispatchers/{id_enterprise_dispatcher}/is-enable',
        description: 'Check if specific Enterprise Dispatcher is disabled',
        security: [['bearerAuth' => []]],
        tags: ['Enterprise Dispatchers'],
        parameters: [
            new OA\Parameter(name: 'id_enterprise_dispatcher', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200, description: 'Success response',
                content: new OA\JsonContent(
                    properties: [new OA\Property(property: 'is_enable', type: 'boolean')],
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
        ],
    )]
    #[Route('/solidCP/enterprise-dispatchers/{id_enterprise_dispatcher}/is-enable', name: 'apiEnterpriseDispatchers.isEnable', methods: ['GET'])]
    public function isEnable(int $id_enterprise_dispatcher, IsEnable\Handler $handler): Response
    {
        $command = new IsEnable\Command($id_enterprise_dispatcher);

        $violations = $this->validator->validate($command);
        if (\count($violations)) {
            $json = $this->serializer->serialize($violations, 'json');
            return new JsonResponse($json, Response::HTTP_BAD_REQUEST, [], true);
        }

        $isEnable = $handler->handle($command); //catch exceptions from Events in DomainExceptionFormatter

        return $this->json(['is_enable' => $isEnable], Response::HTTP_OK);
    }

}