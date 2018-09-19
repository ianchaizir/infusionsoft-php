<?php

namespace Infusionsoft\Http;

use fXmlRpc\Transport\GuzzleBridge;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Psr\Log\LoggerInterface;

class GuzzleHttpClient extends Client implements ClientInterface
{

    public $debug;
    public $httpLogAdapter;

    public function __construct($debug, LoggerInterface $httpLogAdapter)
    {
        $this->debug          = $debug;
        $this->httpLogAdapter = $httpLogAdapter;

        $config = ['timeout' => 60];

        parent::__construct($config);
    }

    /**
     * @return \fXmlRpc\Transport\TransportInterface
     */
    public function getXmlRpcTransport()
    {
        return new GuzzleBridge(new \Guzzle\Http\Client());
    }

    /**
     * Sends a request to the given URI and returns the raw response.
     *
     * @param string $method
     * @param string $uri
     * @param array $options
     *
     * @return mixed
     * @throws HttpException
     */
    public function request($method, $uri = null, array $options = [])
    {
        if ( ! isset($options['headers'])) {
            $options['headers'] = [];
        }

        if ( ! isset($options['body'])) {
            $options['body'] = null;
        }

        try {
            $request = $this->createRequest($method, $uri,$options);
            $response = $this->send($request);

            return $response->getBody();
        } catch (BadResponseException $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
