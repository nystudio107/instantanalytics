<?php

namespace TheIconic\Tracking\GoogleAnalytics\Network;

use TheIconic\Tracking\GoogleAnalytics\AnalyticsResponse;
use TheIconic\Tracking\GoogleAnalytics\Parameters\General\CacheBuster;
use TheIconic\Tracking\GoogleAnalytics\Parameters\SingleParameter;
use TheIconic\Tracking\GoogleAnalytics\Parameters\CompoundParameterCollection;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Promise;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class HttpClient
 *
 * @package TheIconic\Tracking\GoogleAnalytics
 */
class HttpClient
{
    /**
     * User agent for the client.
     */
    const PHP_GA_MEASUREMENT_PROTOCOL_USER_AGENT =
        'THE ICONIC GA Measurement Protocol PHP Client (https://github.com/theiconic/php-ga-measurement-protocol)';

    /**
     * Timeout in seconds for the request connection and actual request execution.
     * Using the same value you can find in Google's PHP Client.
     */
    const REQUEST_TIMEOUT_SECONDS = 100;

    /**
     * HTTP client.
     *
     * @var Client
     */
    private $client;

    /**
     * @var array
     */
    private $payloadParameters;

    /**
     * @var string
     */
    private $cacheBuster = '';

    /**
     * Holds the promises (async responses).
     *
     * @var PromiseInterface[]
     */
    private static $promises = [];

    /**
     * We have to unwrap and send all promises at the end before analytics objects is destroyed.
     */
    public function __destruct()
    {
        Promise\unwrap(self::$promises);
    }

    /**
     * Sets HTTP client.
     *
     * @param Client $client
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return array
     */
    public function getPayloadParameters()
    {
        return $this->payloadParameters;
    }

    /**
     * Gets HTTP client for internal class use.
     *
     * @return Client
     */
    private function getClient()
    {
        if ($this->client === null) {
            // @codeCoverageIgnoreStart
            $this->setClient(new Client());
        }
        // @codeCoverageIgnoreEnd

        return $this->client;
    }

    /**
     * Sends request to Google Analytics.
     *
     * @param string $url
     * @param SingleParameter[] $singleParameters
     * @param CompoundParameterCollection[] $compoundParameters
     * @param boolean $nonBlocking
     * @return AnalyticsResponse
     */
    public function post($url, array $singleParameters, array $compoundParameters, $nonBlocking = false)
    {
        $singlesPost = $this->getSingleParametersPayload($singleParameters);

        $compoundsPost = $this->getCompoundParametersPayload($compoundParameters);

        $this->payloadParameters = array_merge($singlesPost, $compoundsPost);

        if (!empty($this->cacheBuster)) {
            $this->payloadParameters['z'] = $this->cacheBuster;
        }

        $request = new Request(
            'GET',
            $url . '?' . http_build_query($this->payloadParameters),
            ['User-Agent' => self::PHP_GA_MEASUREMENT_PROTOCOL_USER_AGENT]
        );

        $response = $this->getClient()->sendAsync($request, [
            'synchronous' => !$nonBlocking,
            'timeout' => self::REQUEST_TIMEOUT_SECONDS,
            'connect_timeout' => self::REQUEST_TIMEOUT_SECONDS,
        ]);

        if ($nonBlocking) {
            self::$promises[] = $response;
        } else {
            $response = $response->wait();
        }

        return $this->getAnalyticsResponse($request, $response);
    }

    /**
     * Creates an analytics response object.
     *
     * @param RequestInterface $request
     * @param ResponseInterface|PromiseInterface $response
     * @return AnalyticsResponse
     */
    protected function getAnalyticsResponse(RequestInterface $request, $response)
    {
        return new AnalyticsResponse($request, $response);
    }

    /**
     * Prepares all the Single Parameters to be sent to GA.
     *
     * @param SingleParameter[] $singleParameters
     * @return array
     */
    private function getSingleParametersPayload(array $singleParameters)
    {
        $postData = [];
        $cacheBuster = new CacheBuster();

        foreach ($singleParameters as $parameterObj) {
            if ($parameterObj->getName() === $cacheBuster->getName()) {
                $this->cacheBuster = $parameterObj->getValue();
                continue;
            }

            $postData[$parameterObj->getName()] = $parameterObj->getValue();
        }

        return $postData;
    }

    /**
     * Prepares compound parameters inside collections to be sent to GA.
     *
     * @param CompoundParameterCollection[] $compoundParameters
     * @return array
     */
    private function getCompoundParametersPayload(array $compoundParameters)
    {
        $postData = [];

        foreach ($compoundParameters as $compoundCollection) {
            $parameterArray = $compoundCollection->getParametersArray();
            $postData = array_merge($postData, $parameterArray);
        }

        return $postData;
    }
}
