<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Service\SOAP;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseServer\EnterpriseServer;
use App\Model\ControlPanel\Service\NotFoundException;

class SoapExecute
{
    private string $url;
    private array $options;
    private \SoapClient $soapClient;
    private bool $enabledTraceMode = false;

//    protected function __construct(string $url, string $login, string $password, bool $caching = false, bool $compression = true)
//    {
//        $this->url = $url;
//        $this->caching = $caching;
//        $this->compression = $compression;
//        $this->options = [
//            'login' => $login,
//            'password' => $password,
//            //'trace' => 1, //debug
//            //'exceptions' => 0,
//            //'soap_version' => SOAP_1_2,
//            'compression' => (($this->compression) ? (SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP) : ''),
//            'cache_wsdl' => ($this->caching) ? 1 : 0,
//        ];
//
//    }

    public function initFromEnterpriseServer(EnterpriseServer $enterpriseServer, $caching = false, $compression = true): void
    {
//        if(!$enterpriseServer->isEnabled()){ //not good, we have to give a chance to finish service their work, better to prevent it from UseCases
//            throw new \DomainException("The EnterpriseServer {$enterpriseServer->getName()} is disabled");
//        }
        $this->initManual($enterpriseServer->getUrl(), $enterpriseServer->getLogin(), $enterpriseServer->getPassword(), $caching, $compression);
    }

    public function initManual(string $url, string $login, string $password, bool $keepAlive = false, bool $caching = false, bool $compression = true): void
    {
        $this->url = $url;
        $this->options = [
            'login' => $login,
            'password' => $password,
            //'trace' => 1, //debug
            //'exceptions' => 0,
            //'soap_version' => SOAP_1_2,
            'keep_alive' => $keepAlive,
            'compression' => (($compression) ? (SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP) : ''),
            'cache_wsdl' => ($caching) ? 1 : 0,
        ];
//        $this->options = [
//            'login' => $login,
//            'password' => $password,
//            'verifypeer' => false,
//            'verifyhost' => false,
//            'trace' => true,
//            'connection_timeout' => 5000,
//            'soap_version' => SOAP_1_2,
//            'stream_context' => stream_context_create([
//                'http' => [
//                    'protocol_version' => '1.0',
//                    'header' => 'Connection: Close'
//                ]
//            ])
//        ];
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setOptions(bool $keepAlive = false, bool $caching = false, bool $compression = true): void
    {
        if(!isset($this->options['login'])){
            throw new NotFoundException("The login was not found. First init the class");
        }
        $this->options = [
            'login' => $this->options['login'],
            'password' => $this->options['password'],
            //'trace' => 1, //debug
            //'exceptions' => 0,
            //'soap_version' => SOAP_1_2,
            'keep_alive' => $keepAlive,
            'compression' => (($compression) ? (SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP) : ''),
            'cache_wsdl' => ($caching) ? 1 : 0,
        ];
    }

    public function enableTraceMode(): void
    {
        //$clone = clone $this;
        $this->options['trace'] = $this->enabledTraceMode = true;
        //return $this;
    }

    /**
     * @param string $service
     * @param string $method
     * @param array $params
     * @return mixed
     * @throws \Exception
     */
    protected function execute(string $service, string $method, array $params): mixed
    {
        $result = $this->executeInternal($service, $method, $params);

        if (count((array)$result) === 0) {
            throw new NotFoundException("Not Found " . implode(", ", $params). " in the method: $method");
        }

        return $result;
    }

    /**
     * @param string $service
     * @param string $method
     * @param array $params
     * @return mixed
     * @throws \Exception
     */
    private function executeInternal(string $service, string $method, array $params): mixed
    {
        $host = $this->url . "/{$service}?WSDL";
        try {
            $this->soapClient = new \SoapClient($host, $this->options);
            // Execute the request and process the results
            return call_user_func(array($this->soapClient, $method), $params);
        } catch (\SoapFault $e) {
            throw new \Exception("SOAP Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()})", $e->getCode(), $e);
        } catch (\Exception $e) {
            throw new \Exception("General Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()})", $e->getCode(),$e);
        }
    }

    /**
     * Converts an object or an XML string to an array
     *
     * @param mixed $value Object or an XML string
     * @param boolean $loadXml Loads the string into the SimpleXML object
     * @return array
     */
    protected function convertArray(mixed $value, bool $loadXml = false): array
    {
        // This is silly, but it works, and it works very well for what we are doing :)
        // copy-pasted from SolidCP php module https://solidcp.com
        return json_decode(json_encode(($loadXml ? simplexml_load_string($value) : $value)), true);
    }

    public function showDump(): void
    {
        if (!$this->enabledTraceMode) {
            throw new \Exception("SOAP trace mode is not enabled");
        }
        dump($this->soapClient->__getLastResponse());
        dump($this->soapClient->__getLastResponseHeaders());
        dump($this->soapClient->__getLastRequestHeaders());
        dump($this->soapClient->__getLastRequest());
    }
}