<?php
declare(strict_types=1);

namespace App\Controller\Api\ControlPanel\SolidCP;

use App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\AllinOne;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class HomeController extends AbstractController
{
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;

    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    /**
     * @OA\Post(
     *     path="/solidCP/all-in-one/user/package/vps",
     *     tags={"Create SolidCP All in One User/Package/Vps"},
     *     description="Correct way to create VPS for SolidCP panel. It will automatically create user, package and VPS if needed.",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             required={
     *                      "client_login", "client_email", "client_password",
     *                      "server_package_name", "server_location_name", "server_os_name", "server_password", "server_ip_amount"
     *              },
     *             @OA\Property(property="client_login", type="string", description="The login that the user will use for the SolidCP panel"),
     *             @OA\Property(property="client_email", type="string", example="example@mail.ufo", description="The email that the user will use in the SolidCP panel, if forget password"),
     *             @OA\Property(property="client_password", type="string", description="Generated password for SolidCP client"),
     *             @OA\Property(property="server_package_name", type="string", example="Silver RDP12"),
     *             @OA\Property(property="server_location_name", type="string", example="Meppel, Netherlands"),
     *             @OA\Property(property="server_os_name", type="string", example="Windows Server 2019"),
     *             @OA\Property(property="server_password", type="string", description="Generated password for VM server"),
     *             @OA\Property(property="server_ip_amount", type="integer", description="The number of IP addresses to be assigned to the VM server."),
     *             @OA\Property(property="id_enterprise_dispatcher", type="integer", description="if not selected, the default is used. No need to choose if only one enterprise is used"),
     *             @OA\Property(property="ignore_node_ids", type="array",
     *                  @OA\Items(
     *                      @OA\Property(type="integer")
     *                  ), example={1,2,3}, description="nodes ids that need to ignore for installation (all storages that related to this node). For example, if the old installation is not yet complete"),
     *             @OA\Property(property="ignore_hosting_space_ids", type="array",
     *                  @OA\Items(
     *                      @OA\Property(type="integer")
     *                  ), example={1,2,3}, description="hostingspace ids (storages) that need to ignore for installation. For example, if the old installation is not yet complete"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=202,
     *         description="Accepted response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="is_user_exists", type="boolean", description="if false, this user was not created, but the old one with the same login was used (the password was not changed)"),
     *             @OA\Property(property="solidcp_package_id", type="integer", description="The package in which the virtual machine is installed"),
     *             @OA\Property(property="vps", type="object", description="The VPS details",
     *                 @OA\Property(property="solidcp_item_id", type="integer", description="VM SolidCP ID"),
     *                 @OA\Property(property="provisioning_status", type="string"),
     *                 @OA\Property(property="main_ip", type="string", example="192.168.0.2"),
     *                 @OA\Property(property="secondary_ips", type="array",
     *                      @OA\Items(
     *                          @OA\Property(type="string")
     *                      ), example={"192.168.0.4","192.168.0.5"}
     *                 ),
     *             ),
     *             @OA\Property(property="solidcp_server_node", type="object", description="The Node/HDD in which the virtual machine was installed.",
     *                 @OA\Property(property="node_id", type="integer", description="SolidCP HyperV node"),
     *                 @OA\Property(property="solidcp_hosting_space_id", type="integer", description="SolidCP hosting space aka Storage HDD/SSD/NAS/etc"),
     *             ),
     *             @OA\Property(property="link", type="object", description="Get VPS Provisioning Status. Whether the server is prepared or not, until returns OK or Error",
     *                 @OA\Property(property="rel", type="string"),
     *                 @OA\Property(property="action", type="string"),
     *                 @OA\Property(property="href", type="string"),
     *            ),
     *         ),
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
     * @Route("/solidCP/all-in-one/user/package/vps", name="createAllInOneVm", methods={"POST"})
     * @param Request $request
     * @param AllinOne\Create\VM\Handler $handler
     * @return Response
     * @throws \Exception
     */
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
            'href' => '/api'.$this->generateUrl('vps.vpsProvisioningStatus', ['solidcp_item_id' => $arrayResult['vps']['solidcp_item_id']]),
        ];

        return $this->json([$arrayResult], Response::HTTP_ACCEPTED);
    }
}