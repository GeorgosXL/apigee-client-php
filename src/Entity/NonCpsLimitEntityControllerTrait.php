<?php

namespace Apigee\Edge\Entity;

/**
 * Trait NonCpsLimitEntityControllerTrait.
 *
 * @package Apigee\Edge\Entity
 * @author Dezső Biczó <mxr576@gmail.com>
 * @see \Apigee\Edge\Entity\NonCpsLimitEntityControllerInterface
 */
trait NonCpsLimitEntityControllerTrait
{
    /**
     * @inheritdoc
     */
    public function getEntities(): array
    {
        $entities = [];
        $query_params = [
            'expand' => 'true',
        ];
        $uri = $this->getBaseEndpointUri()->withQuery(http_build_query($query_params));
        $response = $this->client->get($uri);
        $responseArray = $this->parseResponseToArray($response);
        foreach ($responseArray as $item) {
            /** @var \Apigee\Edge\Entity\EntityInterface $tmp */
            $tmp = $this->entitySerializer->denormalize($item, $this->entityFactory->getEntityByController($this));
            $entities[$tmp->id()] = $tmp;
        }
        return $entities;
    }

    /**
     * @inheritdoc
     */
    public function getEntityIds(): array
    {
        $query_params = [
            'expand' => 'false',
        ];
        $uri = $this->getBaseEndpointUri()->withQuery(http_build_query($query_params));
        $response = $this->client->get($uri);
        return $this->parseResponseToArray($response);
    }
}