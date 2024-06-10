<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Service\SOAP;

use App\Model\ControlPanel\Service\NotFoundException;

abstract class BaseSoapExecute implements SoapExecuteInterface
{
    protected string $url;
    private array $options;
    private \SoapClient $soapClient;
    private bool $enabledTraceMode = false;

    #[\Override]
    public function initManual(
        string $url, string $login, string $password, bool $caching = false, bool $compression = true, bool $keepAlive = false
    ): void
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
/*        $this->options = [
            'login' => $login,
            'password' => $password,
            'verifypeer' => false,
            'verifyhost' => false,
            'trace' => true,
            'connection_timeout' => 5000,
            'soap_version' => SOAP_1_2,
            'stream_context' => stream_context_create([
                'http' => [
                    'protocol_version' => '1.0',
                    'header' => 'Connection: Close'
                ]
            ])
        ];*/
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    protected abstract function getServiceWsdl(): string;

    public function setOptions(bool $keepAlive = false, bool $caching = false, bool $compression = true): void
    {
        if (!isset($this->options['login'])) {
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
     * @throws \SoapFault
     */
    protected function createSoapClient(): \SoapClient
    {
        return new \SoapClient($this->getServiceWsdl(), $this->options);
    }

    /**
     * @throws \SoapFault
     */
    protected function execute(string $method, array $params): mixed
    {
        $result = $this->executeInternal($method, $params);

        if (count((array)$result) === 0) {
            throw new NotFoundException("Not Found " . implode(", ", $params) . " in the method: $method");
        }

        return $result;
    }

    /**
     * @throws \SoapFault
     * @throws \Exception
     */
    private function executeInternal(string $method, array $params): mixed
    {
        //$host = $this->url . "/{$service}?WSDL";
        try {
            $this->soapClient = $this->createSoapClient();
            // Execute the request and process the results
            return $this->soapClient->__soapCall($method, [$params]); //return call_user_func([$this->soapClient, $method], $params);
        } catch (\SoapFault $e) {
            throw new \SoapFault($e->faultcode, "SOAP Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()})");
        } catch (\Exception $e) {
            throw new \Exception("General Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()})", $e->getCode(), $e);
        }
    }

    /**
     * Converts an object or an XML string to an array
     */
    protected function convertArray(mixed $value, bool $loadXml = false): array
    {
        // This is silly, but it works, and it works very well for what we are doing :)
        // copy-pasted from SolidCP php module https://solidcp.com
        return json_decode(json_encode(($loadXml ? simplexml_load_string((string)$value) : $value)), true);
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
        dump($this->soapClient->__getCookies());
    }
}