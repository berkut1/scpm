<?php
declare(strict_types=1);

namespace App\Controller\Api\ControlPanel\SolidCP;

use App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\VirtualizationServer2012;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class VpsController extends AbstractController
{
    private SerializerInterface $serializer;
    private DenormalizerInterface $denormalizer;
    private ValidatorInterface $validator;

    public function __construct(SerializerInterface $serializer, DenormalizerInterface $denormalizer, ValidatorInterface $validator)
    {
        $this->serializer = $serializer;
        $this->denormalizer = $denormalizer;
        $this->validator = $validator;
    }

    /**
     * @OA\Get(
     *     path="/solidCP/vps/{solidcp_item_id}/provisioning-status",
     *     tags={"VPS"},
     *     description="Get VPS Provisioning Status. Whether the server is prepared or not. Should be used after calling All in One User/Package/Vps",
     *     @OA\Parameter(
     *         in="path",
     *         name="solidcp_item_id", description="the id that provides the VPS creation method",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
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
     *             @OA\Property(property="provisioning_status", type="string", description="OK, Error, InProgress"),
     *             @OA\Property(property="task", type="object",
     *                 @OA\Property(property="percent_complete", type="integer"),
     *                 @OA\Property(property="creation_time", type="string"),
     *                 @OA\Property(property="status", type="string"),
     *             ),
     *             @OA\Property(property="link", type="object", nullable=true,
     *                 @OA\Property(property="rel", type="string"),
     *                 @OA\Property(property="action", type="string"),
     *                 @OA\Property(property="href", type="string"),
     *             ),
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
    #[Route('/solidCP/vps/{solidcp_item_id}/provisioning-status', name: 'apiVps.vpsProvisioningStatus', requirements: ['solidcp_item_id' => '\d+'], methods: ['GET'])]
    public function vpsProvisioningStatus(int $solidcp_item_id, Request $request, VirtualizationServer2012\Check\VpsProvisioningStatus\Handler $handler): Response
    {
        $command = new VirtualizationServer2012\Check\VpsProvisioningStatus\Command($solidcp_item_id, (int)$request->query->get('id_enterprise_dispatcher'));

        $violations = $this->validator->validate($command);
        if (\count($violations)) {
            $json = $this->serializer->serialize($violations, 'json');
            return new JsonResponse($json, Response::HTTP_BAD_REQUEST, [], true);
        }

        $result = $handler->handle($command); //catch exceptions from Events in DomainExceptionFormatter
        if ($result['provisioning_status'] === 'OK') {
            $link = [
                'rel' => 'state',
                'action' => 'GET',
                'href' => '/api' . $this->generateUrl('apiVps.vpsState', ['solidcp_item_id' => $solidcp_item_id]),
            ];
        } else {
            $link = [
                'rel' => 'self',
                'action' => 'GET',
                'href' => '/api' . $this->generateUrl('apiVps.vpsProvisioningStatus', ['solidcp_item_id' => $solidcp_item_id]),
            ];
        }
        $result['link'] = $link;

        return $this->json([
            $result,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/solidCP/vps/{solidcp_item_id}/state",
     *     tags={"VPS"},
     *     description="Get state of VM Running/Stopped/ets. Must be used only if VPS Provisioning Status shows - OK",
     *     @OA\Parameter(
     *         in="path",
     *         name="solidcp_item_id", description="the id that provides the VPS creation method",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
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
     *             @OA\Property(property="state", type="string", example="Snapshotting or Migrating or Deleted or Unknown or Other or Running or Off or Stopping or Saved or Paused or Starting or Reset or Saving or Pausing or Resuming or FastSaved or FastSaving or RunningCritical or OffCritical or StoppingCritical or SavedCritical or PausedCritical or StartingCritical or ResetCritical or SavingCritical or PausingCritical or ResumingCritical or FastSavedCritical or FastSavingCritical",
     *                          description="After installation, we need to get the state = Running, everything else in this case means a problem. If state = Starting, then need to wait."),
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
    #[Route('/solidCP/vps/{solidcp_item_id}/state', name: 'apiVps.vpsState', requirements: ['solidcp_item_id' => '\d+'], methods: ['GET'])]
    public function vpsState(int $solidcp_item_id, Request $request, VirtualizationServer2012\Check\VpsState\ByItemId\Handler $handler): Response
    {
        $command = new VirtualizationServer2012\Check\VpsState\ByItemId\Command($solidcp_item_id, (int)$request->query->get('id_enterprise_dispatcher'));

        $violations = $this->validator->validate($command);
        if (\count($violations)) {
            $json = $this->serializer->serialize($violations, 'json');
            return new JsonResponse($json, Response::HTTP_BAD_REQUEST, [], true);
        }

        $result = $handler->handle($command); //catch exceptions from Events in DomainExceptionFormatter

        return $this->json([
            'state' => $result,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/solidCP/vps/ip/{vps_ip_address}/state",
     *     tags={"VPS"},
     *     description="Get state of VM Running/Stopped/ets. Use it only if you need to check a state of a VM after changing the VPS status",
     *     @OA\Parameter(
     *         in="path",
     *         name="vps_ip_address", description="VM IPv4 address",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="ipv4"
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
     *             @OA\Property(property="state", type="string", example="Snapshotting or Migrating or Deleted or Unknown or Other or Running or Off or Stopping or Saved or Paused or Starting or Reset or Saving or Pausing or Resuming or FastSaved or FastSaving or RunningCritical or OffCritical or StoppingCritical or SavedCritical or PausedCritical or StartingCritical or ResetCritical or SavingCritical or PausingCritical or ResumingCritical or FastSavedCritical or FastSavingCritical",
     *                          description="After installation, we need to get the state = Running, everything else in this case means a problem. If state = Starting, then need to wait."),
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
    #[Route('/solidCP/vps/ip/{vps_ip_address}/state', name: 'apiVps.vpsStateByIp', methods: ['GET'])]
    public function vpsStateByIp(string $vps_ip_address, Request $request, VirtualizationServer2012\Check\VpsState\ByIP\Handler $handler): Response
    {
        $command = new VirtualizationServer2012\Check\VpsState\ByIP\Command();
        /** @var VirtualizationServer2012\Check\VpsState\ByIP\Command $command */
        $command = $this->denormalizer->denormalize($request->query->all(), VirtualizationServer2012\Check\VpsState\ByIP\Command::class, 'array', [
            'object_to_populate' => $command, //got prop from AbstractObjectNormalizer::
            //'ignored_attributes' => ['id_enterprise_dispatcher'],
            'disable_type_enforcement' => true //https://github.com/symfony/symfony/issues/32167#issuecomment-510241190
        ]);
        $command->vps_ip_address = $vps_ip_address;

        $violations = $this->validator->validate($command);
        if (\count($violations)) {
            $json = $this->serializer->serialize($violations, 'json');
            return new JsonResponse($json, Response::HTTP_BAD_REQUEST, [], true);
        }

        $result = $handler->handle($command); //catch exceptions from Events in DomainExceptionFormatter

        return $this->json([
            'state' => $result,
        ]);
    }

    /**
     * @OA\Put(
     *     path="/solidCP/users/{client_login}/vps/{vps_ip_address}/state",
     *     tags={"VPS"},
     *     description="Change VPS state over IPv4, that assigned to the client client_login. (Start or TurnOff or ShutDown or Reset or Pause or Save or Reboot or Resume)",
     *     @OA\Parameter(
     *         in="path",
     *         name="client_login", description="SolidCP client login",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         in="path",
     *         name="vps_ip_address", description="VM IPv4 address",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="ipv4"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             required={"vps_state"},
     *             @OA\Property(property="vps_state", type="string", enum={"Start", "TurnOff", "ShutDown", "Reset", "Pause", "Save", "Reboot", "Resume"}),
     *             @OA\Property(property="id_enterprise_dispatcher", type="integer", description="if not selected, the default is used. No need to choose if only one enterprise is used"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="IsSuccess", type="bool", description="Return true or false if Success"),
     *             @OA\Property(property="ErrorCodes", type="array",
     *                     @OA\Items(
     *                         @OA\Property(type="string")
     *                     )
     *             ),
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
    #[Route('/solidCP/users/{client_login}/vps/{vps_ip_address}/state', name: 'apiVps.changeStateByIpAddress', methods: ['PUT'])]
    public function changeStateByIpAddress(string $client_login, string $vps_ip_address, Request $request, VirtualizationServer2012\ChangeState\Handler $handler): Response
    {
        /** @var VirtualizationServer2012\ChangeState\Command $command */
        $command = $this->serializer->deserialize($request->getContent(), VirtualizationServer2012\ChangeState\Command::class, 'json');
        $command->client_login = $client_login;
        $command->vps_ip_address = $vps_ip_address;
        //$command->id_enterprise_dispatcher = (int)$request->query->get('id_enterprise_dispatcher');

        $violations = $this->validator->validate($command);
        if (\count($violations)) {
            $json = $this->serializer->serialize($violations, 'json');
            return new JsonResponse($json, Response::HTTP_BAD_REQUEST, [], true);
        }

        $result = $handler->handle($command); //catch exceptions from Events in DomainExceptionFormatter

        return $this->json([$result], Response::HTTP_OK);
    }

    /**
     * @OA\Put(
     *     path="/solidCP/users/{client_login}/vps/{vps_ip_address}/status",
     *     tags={"VPS"},
     *     description="Change VPS and its packet status over IPv4, that assigned to the client client_login. Active/Suspended/Cancelled",
     *     @OA\Parameter(
     *         in="path",
     *         name="client_login", description="SolidCP client login",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         in="path",
     *         name="vps_ip_address", description="VM IPv4 address",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="ipv4"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             required={"vps_status"},
     *             @OA\Property(property="vps_status", type="string", enum={"Active", "Suspended", "Cancelled"}),
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
    #[Route('/solidCP/users/{client_login}/vps/{vps_ip_address}/status', name: 'apiVps.changeStatusByIpAddress', methods: ['PUT'])]
    public function changeStatusByIpAddress(string $client_login, string $vps_ip_address, Request $request, VirtualizationServer2012\ChangeStatus\Handler $handler): Response
    {
        /** @var VirtualizationServer2012\ChangeStatus\Command $command */
        $command = $this->serializer->deserialize($request->getContent(), VirtualizationServer2012\ChangeStatus\Command::class, 'json');
        $command->client_login = $client_login;
        $command->vps_ip_address = $vps_ip_address;
        //$command->id_enterprise_dispatcher = (int)$request->query->get('id_enterprise_dispatcher');

        $violations = $this->validator->validate($command);
        if (\count($violations)) {
            $json = $this->serializer->serialize($violations, 'json');
            return new JsonResponse($json, Response::HTTP_BAD_REQUEST, [], true);
        }

        $handler->handle($command); //catch exceptions from Events in DomainExceptionFormatter

        return $this->json([], Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\Get(
     *     path="/solidCP/vps/available-spaces/count",
     *     tags={"VPS"},
     *     description="Provides the number of available spaces for a specific package",
     *     @OA\Parameter(
     *         name="server_package_name", example="Silver RDP23",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         style="form"
     *     ),
     *     @OA\Parameter(
     *         name="server_location_name", example="Meppel, Netherlands",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         style="form"
     *     ),
     *     @OA\Parameter(
     *         name="server_ip_amount",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         style="form"
     *     ),
     *     @OA\Parameter(
     *         name="id_enterprise_dispatcher", description="if not selected, the default is used. No need to choose if only one enterprise is used",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer"),
     *         style="form"
     *     ),
     *     @OA\Parameter(
     *         name="ignore_node_ids[]",
     *         in="query",
     *         description="nodes ids that need to ignore for installation. For example, if the old installation is not yet complete",
     *         required=false,
     *         @OA\Schema(type="array", @OA\Items(type="integer"),),
     *         style="form"
     *     ),
     *     @OA\Parameter(
     *         name="ignore_hosting_space_ids[]",
     *         in="query",
     *         description="hostingspace ids (storages) that need to ignore for installation. For example, if the old installation is not yet complete",
     *         required=false,
     *         @OA\Schema(type="array", @OA\Items(type="integer"),),
     *         style="form"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="number_available_spaces", type="integer"),
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
    #[Route('/solidCP/vps/available-spaces/count', name: 'apiVps.vpsAvailableSpacesCount', methods: ['GET'])]
    public function vpsAvailableSpacesCount(Request $request, VirtualizationServer2012\AvailableSpacePlan\Handler $handler): Response
    {
        $command = new VirtualizationServer2012\AvailableSpacePlan\Command();
        /** @var VirtualizationServer2012\AvailableSpacePlan\Command $command */
        $command = $this->denormalizer->denormalize($request->query->all(), VirtualizationServer2012\AvailableSpacePlan\Command::class, 'array', [
            'object_to_populate' => $command, //got prop from AbstractObjectNormalizer::
            //'ignored_attributes' => ['id_enterprise_dispatcher'],
            'disable_type_enforcement' => true //https://github.com/symfony/symfony/issues/32167#issuecomment-510241190
        ]);

        $violations = $this->validator->validate($command);
        if (\count($violations)) {
            $json = $this->serializer->serialize($violations, 'json');
            return new JsonResponse($json, Response::HTTP_BAD_REQUEST, [], true);
        }

        $result = $handler->handle($command); //catch exceptions from Events in DomainExceptionFormatter

        return $this->json([
            'number_available_spaces' => count($result),
        ]);
    }
}