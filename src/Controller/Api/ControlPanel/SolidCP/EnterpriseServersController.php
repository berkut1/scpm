<?php
declare(strict_types=1);

namespace App\Controller\Api\ControlPanel\SolidCP;

use App\Model\ControlPanel\UseCase\Panel\SolidCP\EnterpriseServer\IsEnable;

use App\ReadModel\ControlPanel\Panel\SolidCP\EnterpriseServer\EnterpriseServerFetcher;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EnterpriseServersController extends AbstractController
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
     *     path="/solidCP/enterprise-servers",
     *     tags={"Get List of Enterprise Servers"},
     *     description="Get list of Enterprise Servers, use only if you have more than one Enterprise Server",
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
     *     security={{"bearerAuth":{}}}
     * )
     * @Route("/solidCP/enterprise-servers", name="enterpriseServer.allList", methods={"GET"})
     * @param EnterpriseServerFetcher $enterpriseServerFetcher
     * @return Response
     */
    public function allList(EnterpriseServerFetcher $enterpriseServerFetcher): Response
    {
        $enterpriseServers = $enterpriseServerFetcher->allList();

        return $this->json([$enterpriseServers], Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/solidCP/enterprise-server/is-enable",
     *     tags={"Is Enterprise Server Enable?"},
     *     description="Check if Enterprise Server is manually disabled",
     *     @OA\Parameter(
     *         name="id_enterprise",
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
     *             @OA\Property(property="isEnable", type="boolean"),
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
     *     security={{"bearerAuth":{}}}
     * )
     * @Route("/solidCP/enterprise-server/is-enable", name="enterpriseServer.isEnable", methods={"GET"})
     * @param Request $request
     * @param IsEnable\Handler $handler
     * @return Response
     * @throws \Exception
     */
    public function isEnable(Request $request, IsEnable\Handler $handler): Response
    {
        $command = new IsEnable\Command((int)$request->query->get('id_enterprise'));

        $violations = $this->validator->validate($command);
        if (\count($violations)) {
            $json = $this->serializer->serialize($violations, 'json');
            return new JsonResponse($json, Response::HTTP_BAD_REQUEST, [], true);
        }

        $isEnable = $handler->handle($command); //catch exceptions from Events in DomainExceptionFormatter

        return $this->json(['isEnable' => $isEnable], Response::HTTP_OK);
    }

}