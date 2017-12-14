<?php

namespace Apigee\Edge\Tests\Test\Mock;

use Http\Mock\Client;
use Psr\Http\Message\RequestInterface;

/**
 * Class MockHttpClient.
 *
 * Adds an additional getter to the Mock client until this PR is not going to be merged.
 *
 * @see https://github.com/php-http/mock-client/pull/23
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class MockHttpClient extends Client implements MockClientInterface
{
    protected $lastRequest;

    public function sendRequest(RequestInterface $request)
    {
        $this->lastRequest = $request;

        return parent::sendRequest($request);
    }

    /**
     * @return \Psr\Http\Message\RequestInterface
     */
    public function getLastRequest(): RequestInterface
    {
        return $this->lastRequest;
    }
}
