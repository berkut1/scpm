<?php
declare(strict_types=1);

namespace App\Controller\ControlPanel\SolidCP;

use App\Model\ControlPanel\Service\SolidCP\HostingSpaceService;
use App\Model\ControlPanel\Service\SolidCP\VirtualizationServer2012Service;
use App\ReadModel\ControlPanel\Panel\SolidCP\EnterpriseServer\EnterpriseServerFetcher;
use App\ReadModel\ControlPanel\Panel\SolidCP\Node\SolidcpServerFetcher;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/panel/solidcp/ajax", name="solidCpAjax")
 * @IsGranted("ROLE_USER")
 */
class AjaxController extends AbstractController
{
    /**
     * @Route("/all-server-array-from-enterprise", name=".allServerArrayFromEnterprise", condition="request.isXmlHttpRequest()"), methods={"GET"}
     * @param Request $request
     * @param SolidcpServerFetcher $fetcher
     * @return JsonResponse
     */
    public function allServerArrayFromEnterprise(Request $request, SolidcpServerFetcher $fetcher): JsonResponse
    {
        $arr = $fetcher->allListFrom((int)$request->query->get('id_enterprise'));
        return new JsonResponse($arr);
    }

    /**
     * @Route("/all-non-added-hosting-space-array-from-enterprise", name=".allNonAddedHostingSpaceArrayFromEnterprise", condition="request.isXmlHttpRequest()"), methods={"GET"}
     * @param Request $request
     * @param HostingSpaceService $fetcher
     * @return JsonResponse
     */
    public function allNonAddedHostingSpaceArrayFromEnterprise(Request $request, HostingSpaceService $fetcher): JsonResponse
    {
        $arr = $fetcher->allNotAddedHostingSpacesFrom((int)$request->query->get('id_enterprise'));
        return new JsonResponse($arr);
    }

    /**
     * @Route("/all-non-added-hosting-space-array-except-hosting-space-id-from-enterprise", name=".allNotAddedHostingSpacesArrayExceptHostingSpaceIdFromEnterprise", condition="request.isXmlHttpRequest()"), methods={"GET"}
     * @param Request $request
     * @param HostingSpaceService $fetcher
     * @return JsonResponse
     */
    public function allNotAddedHostingSpacesArrayExceptHostingSpaceIdFromEnterprise(Request $request, HostingSpaceService $fetcher): JsonResponse
    {
        $arr = $fetcher->allNotAddedHostingSpacesExceptHostingSpaceIdFrom((int)$request->query->get('id_enterprise'), (int)$request->query->get('id_solidcp_hosting_space'));
        return new JsonResponse($arr);
    }

    /**
     * @Route("/all-os-templates-array-from-enterprise-and-packageid", name=".allOsTempaltesArrayFromEnterpriseAndPackageId", condition="request.isXmlHttpRequest()"), methods={"GET"}
     * @param Request $request
     * @param VirtualizationServer2012Service $fetcher
     * @param EnterpriseServerFetcher $enterpriseServerFetcher
     * @return JsonResponse
     */
    public function allOsTempaltesArrayFromEnterpriseAndPackageId(Request $request, VirtualizationServer2012Service $fetcher, EnterpriseServerFetcher $enterpriseServerFetcher): JsonResponse
    {
        $id_enterprise = $request->query->get('id_enterprise');
        if(empty($id_enterprise)){
            $id_enterprise = $enterpriseServerFetcher->getDefault()->getId();
        }

        $arr = $fetcher->allOsTemplateListFrom((int)$id_enterprise, (int)$request->query->get('packageId'));
        return new JsonResponse($arr);
    }
}