<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\ControlPanel\Entity\Panel\SolidCP\Entity\Server\VirtualMachine\VirtualMachine;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @return Response
     */
    public function index(): Response
    {
//        $options = array(
//            'login' => 'reseller',
//            'password' => 'o91Na9caT',
//            'trace' => 1,
//            'exceptions' => 0,
//            'soap_version'=>SOAP_1_2,
//            'cache_wsdl'=>WSDL_CACHE_NONE,
//        );
//        $soapClient = new \SoapClient('http://10.10.10.141:9002/esPackages.asmx?WSDL', $options);
//
////        $result = $soapClient->__soapCall('GetVirtualMachines', [
////            [
////                'packageId' => 135,
////                'filterColumn' => "",
////                'filterValue' => "",
////                'sortColumn' => "",
////                'startRow' => 0,
////                'maximumRows' => 2147483647,
////                'recursive' => true,
////            ]
////        ]);
////        dump($result);
//        $result = $soapClient->__soapCall('GetPackageQuotas', [
//            [
//                'packageId' => 49,
//            ]
//        ]);
//        dump($result);
//        dump($soapClient->__getLastResponse());
////        dump($soapClient->__getLastResponseHeaders());
////        dump($soapClient->__getLastRequestHeaders());
////        dump($soapClient->__getLastRequest());
//        $xml = $soapClient->__getLastRequest();
//        dump($xml);
////        $types = $soapClient->__getFunctions();
////        dump($types);
//
//        $vmSettings = new VirtualMachine(
//            136,
//            2,
//            2048,
//            70,
//            4000,
//            0,
//            0,
//            "test-33.test.local",
//            false,
//            true,
//            true,
//            true,
//            true,
//            true,
//            true,
//            false,
//            false,
//            '',
//            false,
//            0,
//        );

//        $result = $soapClient->__soapCall('CreateNewVirtualMachine', [
//            [
//                'VMSettings' => $vmSettings,
//                'osTemplateFile' => "Windows2012R2x64.vhdx",
//                'password' => "o91Na9caT",
//                'summaryLetterEmail' => null,
//                'externalAddressesNumber' => 0,
//                'randomExternalAddresses' => false,
//                'externalAddresses' => null,
//                'privateAddressesNumber' => 0,
//                'randomPrivateAddresses' => false,
//                'privateAddresses' => null,
//            ]
//        ]);
//
//        dump($soapClient->__getLastResponse());
//        dump($soapClient->__getLastResponseHeaders());
//        dump($soapClient->__getLastRequestHeaders());
//        dump($soapClient->__getLastRequest());

        return $this->render('home.html.twig');
    }
}