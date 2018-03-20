<?php

/**
 * Copyright 2018 Google LLC
 *                            
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *                            
 *      https://www.apache.org/licenses/LICENSE-2.0
 *                            
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Apigee\Edge\Controller;

use Apigee\Edge\Exception\InvalidJsonException;
use Apigee\Edge\HttpClient\Client;
use Apigee\Edge\HttpClient\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;

/**
 * Class AbstractController.
 *
 * Base controller for communicating with Apigee Edge.
 */
abstract class AbstractController
{
    /**
     * @var ClientInterface Client interface that should be used for communication.
     */
    protected $client;

    /** @var \Symfony\Component\Serializer\Encoder\JsonDecode */
    protected $jsonDecoder;

    /**
     * AbstractController constructor.
     *
     * @param ClientInterface|null $client
     */
    public function __construct(ClientInterface $client = null)
    {
        $this->client = $client ?: new Client();
        // Keep the same structure that we get from Edge, do not transforms objects to arrays.
        $this->jsonDecoder = new JsonDecode();
    }

    /**
     * Returns the API endpoint that the controller communicates with.
     *
     * In case of an entity that belongs to an organisation it should return organization/[orgName]/[endpoint].
     *
     * @return UriInterface
     */
    abstract protected function getBaseEndpointUri(): UriInterface;

    /**
     * Decodes an Apigee Edge API response to an associative array.
     *
     * The SDK only works with JSON responses, but let's be prepared for the unexpected.
     *
     * @param ResponseInterface $response
     *
     * @throws \RuntimeException If response can not be decoded, because the input format is unknown.
     * @throws InvalidJsonException If there was an error with decoding a JSON response.
     *
     * @return array
     */
    protected function responseToArray(ResponseInterface $response): array
    {
        if ($response->getHeaderLine('Content-Type') &&
            0 === strpos($response->getHeaderLine('Content-Type'), 'application/json')) {
            try {
                return (array) $this->jsonDecoder->decode((string) $response->getBody(), 'json');
            } catch (UnexpectedValueException $e) {
                throw new InvalidJsonException(
                    $e->getMessage(),
                    $response,
                    $this->client->getJournal()->getLastRequest()
                );
            }
        }
        throw new \RuntimeException(sprintf(
                'Unable to parse response with %s type. Response body: %s',
                $response->getHeaderLine('Content-Type') ?: 'unknown',
                (string) $response->getBody()
            )
        );
    }
}
