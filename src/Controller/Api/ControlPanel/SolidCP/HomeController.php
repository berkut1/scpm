<?php
declare(strict_types=1);

namespace App\Controller\Api\ControlPanel\SolidCP;

use App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\AllinOne;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class HomeController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface  $validator
    ) {}

    #[OA\Post(
        path: '/solidCP/all-in-one/user/package/vps',
        description: 'Correct way to create VPS for SolidCP panel. It will automatically create user, package and VPS if needed.',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                required: ['client_login', 'client_email', 'client_password', 'server_package_name', 'server_location_name', 'server_os_name', 'server_password', 'server_ip_amount'],
                properties: [
                    new OA\Property(property: 'client_login', description: 'The login that the user will use for the SolidCP panel', type: 'string'),
                    new OA\Property(property: 'client_email', description: 'The email that the user will use in the SolidCP panel, if forget password', type: 'string', example: 'example@mail.ufo'),
                    new OA\Property(property: 'client_password', description: 'Generated password for SolidCP client', type: 'string'),
                    new OA\Property(property: 'server_package_name', type: 'string', example: 'Silver RDP12'),
                    new OA\Property(property: 'server_location_name', type: 'string', example: 'Meppel, Netherlands'),
                    new OA\Property(property: 'server_os_name', type: 'string', example: 'Windows Server 2019'),
                    new OA\Property(property: 'server_password', description: 'Generated password for VM server', type: 'string'),
                    new OA\Property(property: 'server_ip_amount', description: 'The number of IP addresses to be assigned to the VM server.', type: 'integer'),
                    new OA\Property(property: 'id_enterprise_dispatcher', description: 'if not selected, the default is used. No need to choose if only one enterprise is used', type: 'integer'),
                    new OA\Property(property: 'ignore_node_ids', description: 'nodes ids that need to ignore for installation (all storages that related to this node). For example, if the old installation is not yet complete', type: 'array',
                        items: new OA\Items(properties: [
                            new OA\Property(type: 'integer'),
                        ]
                        ), example: [1, 2, 3]),
                    new OA\Property(property: 'ignore_hosting_space_ids', description: 'hostingspace ids (storages) that need to ignore for installation. For example, if the old installation is not yet complete', type: 'array',
                        items: new OA\Items(properties: [
                            new OA\Property(type: 'integer'),
                        ]), example: [1, 2, 3]),
                ],
                type: 'object')
        ),
        tags: ['Create SolidCP All in One User/Package/Vps'],
        responses: [
            new OA\Response(
                response: 202,
                description: 'Accepted response',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'is_user_exists', description: 'if false, this user was not created, but the old one with the same login was used (the password was not changed)', type: 'boolean'),
                        new OA\Property(property: 'solidcp_package_id', description: 'The package in which the virtual machine is installed', type: 'integer'),
                        new OA\Property(property: 'vps', description: 'The VPS details',
                            properties: [
                                new OA\Property(property: 'solidcp_item_id', description: 'VM SolidCP ID', type: 'integer'),
                                new OA\Property(property: 'hostname', description: 'VM hostname and name of SolidCP item', type: 'string'),
                                new OA\Property(property: 'provisioning_status', type: 'string'),
                                new OA\Property(property: 'main_ip', type: 'string', example: '192.168.0.2'),
                                new OA\Property(property: 'secondary_ips', type: 'array',
                                    items: new OA\Items(properties: [
                                        new OA\Property(type: 'string'),
                                    ]),
                                    example: ['192.168.0.4', '192.168.0.5']),
                            ],
                            type: 'object'),
                        new OA\Property(property: 'solidcp_server_node', description: 'The Node/HDD in which the virtual machine was installed.',
                            properties: [
                                new OA\Property(property: 'node_id', description: 'SolidCP HyperV node', type: 'integer'),
                                new OA\Property(property: 'solidcp_hosting_space_id', description: 'SolidCP hosting space aka Storage HDD/SSD/NAS/etc', type: 'integer'),
                            ],
                            type: 'object'),
                        new OA\Property(property: 'link', description: 'Get VPS Provisioning Status. Whether the server is prepared or not, until returns OK or Error',
                            properties: [
                                new OA\Property(property: 'rel', type: 'string'),
                                new OA\Property(property: 'action', type: 'string'),
                                new OA\Property(property: 'href', type: 'string'),
                            ],
                            type: 'object'),
                    ], type: 'object')
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
    #[Route('/solidCP/all-in-one/user/package/vps', name: 'apiCreateAllInOneVm', methods: ['POST'])]
    public function createAllInOneVm(Request $request, AllinOne\Create\VM\Handler $handler): Response
    {
        /** @var AllinOne\Create\VM\Command $command */
        $command = $this->serializer->deserialize($request->getContent(), AllinOne\Create\VM\Command::class, 'json');

        $violations = $this->validator->validate($command);
        if (\count($violations)) {
            $json = $this->serializer->serialize($violations, 'json');
            return new JsonResponse($json, Response::HTTP_BAD_REQUEST, [], true);
        }

        $arrayResult = $handler->handle($command); //catch exceptions from Events in DomainExceptionFormatter
        $arrayResult['link'] = [
            'rel' => 'provisioningStatus',
            'action' => 'GET',
            'href' => '/api' . $this->generateUrl('apiVps.vpsProvisioningStatus', ['solidcp_item_id' => $arrayResult['vps']['solidcp_item_id']]),
        ];

        return $this->json([$arrayResult], Response::HTTP_ACCEPTED);
    }
}