<?php
declare(strict_types=1);

namespace App\Controller\Api\ControlPanel\SolidCP;

use App\Model\ControlPanel\UseCase\Panel\SolidCP\EnterpriseDispatcher\IsEnable;

use App\ReadModel\ControlPanel\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcherFetcher;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EnterpriseDispatchersController extends AbstractController
{
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;

    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
    }


    /**
     * @OA\Get(
     *     path="/solidCP/enterprise-dispatchers",
     *     tags={"Enterprise Dispatchers"},
     *     description="Get list of Enterprise Dispatchers, use only if you have more than one Enterprise Dispatchers",
     *     @OA\Response(
     *         response=200,
     *         description="Success response",
     *         @OA\JsonContent(
     *             type="array", description="return ids and names",
     *             @OA\Items(
     *                  @OA\Property(type="string"),
     *              ),
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
     * @Route("/solidCP/enterprise-dispatchers", name="apiEnterpriseDispatchers.allList", methods={"GET"})
     * @param EnterpriseDispatcherFetcher $enterpriseDispatcherFetcher
     * @return Response
     */
    public function allList(EnterpriseDispatcherFetcher $enterpriseDispatcherFetcher): Response
    {
        $enterpriseDispatchers = $enterpriseDispatcherFetcher->allList();

        return $this->json([$enterpriseDispatchers], Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/solidCP/enterprise-dispatchers/default/is-enable",
     *     tags={"Enterprise Dispatchers"},
     *     description="Check if default Enterprise Dispatcher is manually disabled",
     *     @OA\Parameter(
     *         name="id_enterprise_dispatcher",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer"),
     *         style="form",
     *         description="if not pass value, it will use a default"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="is_enable", type="boolean"),
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
     * @Route("/solidCP/enterprise-dispatchers/default/is-enable", name="apiEnterpriseDispatchers.isEnableDefault", methods={"GET"})
     * @param IsEnable\Handler $handler
     * @return Response
     * @throws \Exception
     */
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

    /**
     * @OA\Get(
     *     path="/solidCP/enterprise-dispatchers/{id_enterprise_dispatcher}/is-enable",
     *     tags={"Enterprise Dispatchers"},
     *     description="Check if specific Enterprise Dispatcher is disabled",
     *     @OA\Parameter(
     *         in="path",
     *         name="id_enterprise_dispatcher",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="is_enable", type="boolean"),
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
     * @Route("/solidCP/enterprise-dispatchers/{id_enterprise_dispatcher}/is-enable", name="apiEnterpriseDispatchers.isEnable", methods={"GET"})
     * @param int $id_enterprise_dispatcher
     * @param IsEnable\Handler $handler
     * @return Response
     */
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