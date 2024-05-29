<?php
declare(strict_types=1);

namespace App\Controller\ControlPanel\SolidCP;

use App\Model\ControlPanel\Service\SolidCP\HostingSpaceService;
use App\Model\ControlPanel\Service\SolidCP\VirtualizationServer2012Service;
use App\ReadModel\ControlPanel\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcherFetcher;
use App\ReadModel\ControlPanel\Panel\SolidCP\Node\SolidcpServerFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/panel/solidcp/ajax', name: 'solidCpAjax')]
#[IsGranted('ROLE_MODERATOR')]
final class AjaxController extends AbstractController
{
    #[Route('/all-server-array-from-enterprise', name: '.allServerArrayFromEnterprise', methods: ['GET'], condition: 'request.isXmlHttpRequest()')]
    public function allServerArrayFromEnterprise(Request $request, SolidcpServerFetcher $fetcher): JsonResponse
    {
        $arr = $fetcher->allListFrom((int)$request->query->get('id_enterprise_dispatcher'));
        return new JsonResponse($arr);
    }

    #[Route('/all-non-added-hosting-space-array-from-enterprise', name: '.allNonAddedHostingSpaceArrayFromEnterprise', methods: ['GET'], condition: 'request.isXmlHttpRequest()')]
    public function allNonAddedHostingSpaceArrayFromEnterprise(Request $request, HostingSpaceService $fetcher): JsonResponse
    {
        $arr = $fetcher->allNotAddedHostingSpacesFrom((int)$request->query->get('id_enterprise_dispatcher'));
        return new JsonResponse($arr);
    }

    #[Route('/all-non-added-hosting-space-array-except-hosting-space-id-from-enterprise', name: '.allNotAddedHostingSpacesArrayExceptHostingSpaceIdFromEnterprise', methods: ['GET'], condition: 'request.isXmlHttpRequest()')]
    public function allNotAddedHostingSpacesArrayExceptHostingSpaceIdFromEnterprise(Request $request, HostingSpaceService $fetcher
    ): JsonResponse
    {
        $arr = $fetcher->allNotAddedHostingSpacesExceptHostingSpaceIdFrom((int)$request->query->get('id_enterprise_dispatcher'), (int)$request->query->get('id_solidcp_hosting_space'));
        return new JsonResponse($arr);
    }

    #[Route('/all-os-templates-array-from-enterprise-and-packageid', name: '.allOsTemplatesArrayFromEnterpriseAndPackageId', methods: ['GET'], condition: 'request.isXmlHttpRequest()')]
    public function allOsTemplatesArrayFromEnterpriseAndPackageId(
        Request                         $request,
        VirtualizationServer2012Service $fetcher,
        EnterpriseDispatcherFetcher     $enterpriseDispatcherFetcher
    ): JsonResponse
    {
        $id_enterprise_dispatcher = $request->query->get('id_enterprise_dispatcher');
        if (empty($id_enterprise_dispatcher)) {
            $id_enterprise_dispatcher = $enterpriseDispatcherFetcher->getDefault()->getId();
        }

        $arr = $fetcher->allOsTemplateListFrom((int)$id_enterprise_dispatcher, (int)$request->query->get('packageId'));
        return new JsonResponse($arr);
    }
}