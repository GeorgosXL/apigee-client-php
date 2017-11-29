<?php

namespace Apigee\Edge\Entity;

use Apigee\Edge\Api\Management\Controller\OrganizationController;
use Apigee\Edge\Api\Management\Controller\OrganizationControllerInterface;
use Apigee\Edge\Exception\CpsNotEnabledException;
use Apigee\Edge\Structure\CpsListLimitInterface;

/**
 * Class CpsLimitEntityController.
 *
 * @package Apigee\Edge\Entity
 * @author Dezső Biczó <mxr576@gmail.com>
 * @see \Apigee\Edge\Entity\CpsLimitEntityControllerInterface
 */
abstract class CpsLimitEntityController extends EntityController
{
    /** @var \Apigee\Edge\Api\Management\Controller\OrganizationControllerInterface */
    protected $organizationController;

    /**
     * CpsLimitEntityController constructor.
     *
     * @param string $organization
     * @param null $client
     * @param null $entityFactory
     * @param \Apigee\Edge\Api\Management\Controller\OrganizationControllerInterface $organizationController
     */
    public function __construct(
        $organization,
        $client = null,
        $entityFactory = null,
        OrganizationControllerInterface $organizationController = null
    ) {
        parent::__construct($organization, $client, $entityFactory);
        $this->organizationController = $organizationController ?: new OrganizationController($client, $entityFactory);
    }

    /**
     * @inheritdoc
     */
    public function getEntities(CpsListLimitInterface $cpsLimit = null): array
    {
        $entities = [];
        $query_params = [
            'expand' => 'true',
        ];
        if ($cpsLimit) {
            $query_params['startKey'] = $cpsLimit->getStartKey();
            $query_params['count'] = $cpsLimit->getLimit();
        }
        $uri = $this->getBaseEndpointUri()->withQuery(http_build_query($query_params));
        $response = $this->client->get($uri);
        $responseArray = $this->parseResponseToArray($response);
        // Ignore entity type key from response, ex.: developer.
        $responseArray = reset($responseArray);
        foreach ($responseArray as $item) {
            /** @var \Apigee\Edge\Entity\EntityInterface $tmp */
            $tmp = $this->entitySerializer->denormalize(
                $item,
                $this->entityFactory->getEntityTypeByController($this)
            );
            $entities[$tmp->id()] = $tmp;
        }
        return $entities;
    }

    /**
     * @inheritdoc
     */
    public function getEntityIds(CpsListLimitInterface $cpsLimit = null): array
    {
        $query_params = [
            'expand' => 'false',
        ];
        if ($cpsLimit) {
            $query_params['startKey'] = $cpsLimit->getStartKey();
            $query_params['count'] = $cpsLimit->getLimit();
        }
        $uri = $this->getBaseEndpointUri()->withQuery(http_build_query($query_params));
        $response = $this->client->get($uri);
        return $this->parseResponseToArray($response);
    }

    /**
     * @inheritdoc
     */
    public function createCpsLimit(string $startKey, int $limit): CpsListLimitInterface
    {
        /** @var \Apigee\Edge\Api\Management\Entity\OrganizationInterface $organization */
        $organization = $this->organizationController->load($this->organization);
        if (!$organization->getPropertyValue('features.isCpsEnabled')) {
            throw new CpsNotEnabledException($this->organization);
        }

        // Create an anonymous class here because this class should not exist and be in use
        // in those controllers that do not work with entities that belongs to an organization.
        $cpsLimit = new class() implements CpsListLimitInterface
        {
            protected $startKey;

            protected $limit;

            /**
             * @return string The primary key of the entity that the list should start.
             */
            public function getStartKey(): string
            {
                return $this->startKey;
            }

            /**
             * @return int Number of entities to return.
             */
            public function getLimit(): int
            {
                return $this->limit;
            }

            public function setStartKey(string $startKey): string
            {
                $this->startKey = $startKey;
                return $this->startKey;
            }

            public function setLimit(int $limit): int
            {
                $this->limit = $limit;
                return $this->limit;
            }
        };
        $cpsLimit->setStartKey($startKey);
        $cpsLimit->setLimit($limit);
        return $cpsLimit;
    }
}
