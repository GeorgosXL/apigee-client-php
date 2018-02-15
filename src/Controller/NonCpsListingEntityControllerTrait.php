<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Controller;

/**
 * Trait NonCpsListingEntityControllerTrait.
 *
 * @see \Apigee\Edge\Controller\NonCpsListingEntityControllerInterface
 */
trait NonCpsListingEntityControllerTrait
{
    /**
     * @inheritdoc
     *
     * @psalm-suppress PossiblyNullArrayOffset $tmp->id() is always not null here.
     */
    public function getEntities(): array
    {
        $entities = [];
        $query_params = [
            'expand' => 'true',
        ];
        $uri = $this->getBaseEndpointUri()->withQuery(http_build_query($query_params));
        $response = $this->client->get($uri);
        $responseArray = $this->responseToArray($response);
        // Ignore entity type key from response, ex.: apiProduct.
        $responseArray = reset($responseArray);
        foreach ($responseArray as $item) {
            /** @var \Apigee\Edge\Entity\EntityInterface $tmp */
            $tmp = $this->entitySerializer->denormalize($item, $this->entityFactory->getEntityTypeByController($this));
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

        return $this->responseToArray($response);
    }
}
