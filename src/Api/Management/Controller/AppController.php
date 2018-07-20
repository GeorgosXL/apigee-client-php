<?php

/*
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

namespace Apigee\Edge\Api\Management\Controller;

use Apigee\Edge\Api\Management\Entity\AppInterface;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Controller\PaginatedEntityController;
use Apigee\Edge\Controller\PaginationHelperTrait;
use Apigee\Edge\Structure\PagerInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class AppController.
 */
class AppController extends PaginatedEntityController implements AppControllerInterface
{
    use AppControllerTrait;
    use PaginationHelperTrait;

    protected const ID_GETTER = 'getAppId';

    /**
     * AppController constructor.
     *
     * @param string $organization
     * @param \Apigee\Edge\ClientInterface $client
     * @param \Symfony\Component\Serializer\Normalizer\NormalizerInterface[]|\Symfony\Component\Serializer\Normalizer\DenormalizerInterface[] $entityNormalizers
     * @param \Apigee\Edge\Api\Management\Controller\OrganizationControllerInterface|null $organizationController
     */
    public function __construct(
        string $organization,
        ClientInterface $client,
        array $entityNormalizers = [],
        ?OrganizationControllerInterface $organizationController = null
    ) {
        $entityNormalizers = array_merge($entityNormalizers, $this->appEntityNormalizers());
        parent::__construct($organization, $client, $entityNormalizers, $organizationController);
    }

    /**
     * @inheritdoc
     */
    public function loadApp(string $appId): AppInterface
    {
        $response = $this->client->get($this->getEntityEndpointUri($appId));

        return $this->entityTransformer->denormalize(
            // Pass it as an object, because if serializer would have been used here (just as other places) it would
            // pass an object to the denormalizer and not an array.
            (object) $this->responseToArray($response),
            $this->getEntityClass()
        );
    }

    /**
     * @inheritdoc
     */
    public function listAppIds(PagerInterface $pager = null): array
    {
        return $this->listEntityIds($pager, []);
    }

    /**
     * @inheritdoc
     */
    public function listApps(bool $includeCredentials = true, PagerInterface $pager = null): array
    {
        $queryParams = [
            'includeCred' => $includeCredentials ? 'true' : 'false',
        ];

        return $this->getEntities($pager, $queryParams);
    }

    /**
     * @inheritdoc
     */
    public function listAppIdsByStatus(string $status, PagerInterface $pager = null): array
    {
        $queryParams = [
            'status' => $status,
        ];

        return $this->listEntityIds($pager, $queryParams);
    }

    /**
     * @inheritdoc
     */
    public function listAppsByStatus(
        string $status,
        bool $includeCredentials = true,
        PagerInterface $pager = null
    ): array {
        $queryParams = [
            'status' => $status,
            'includeCred' => $includeCredentials ? 'true' : 'false',
        ];

        return $this->getEntities($pager, $queryParams);
    }

    /**
     * @inheritdoc
     */
    public function listAppIdsByType(string $appType, PagerInterface $pager = null): array
    {
        $queryParams = [
            'apptype' => $appType,
        ];

        return $this->listEntityIds($pager, $queryParams);
    }

    /**
     * @inheritdoc
     */
    public function listAppIdsByFamily(string $appFamily, PagerInterface $pager = null): array
    {
        $queryParams = [
            'appfamily' => $appFamily,
        ];

        return $this->listEntityIds($pager, $queryParams);
    }

    /**
     * @inheritdoc
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        return $this->client->getUriFactory()->createUri("/organizations/{$this->organization}/apps");
    }

    /**
     * @inheritdoc
     */
    protected function getEntityClass(): string
    {
        // It could be either a developer- or a company app. The AppDenormalizer can automatically decide which one
        // should be used.
        return AppInterface::class;
    }

    /**
     * @inheritdoc
     */
    protected function getEntities(
        PagerInterface $pager = null,
        array $query_params = [],
        string $idGetter = null
    ): array {
        $idGetter = $idGetter ?? static::ID_GETTER;

        return $this->listEntities($pager, $query_params, $idGetter);
    }
}
