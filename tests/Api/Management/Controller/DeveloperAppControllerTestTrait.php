<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Tests\Api\Management\Controller;

use Apigee\Edge\Api\Management\Controller\ApiProductController;
use Apigee\Edge\Api\Management\Controller\DeveloperController;
use Apigee\Edge\Exception\ApiException;
use Apigee\Edge\Exception\ClientErrorException;
use Apigee\Edge\Tests\Test\Mock\TestClientFactory;

/**
 * Trait DeveloperAppControllerTestTrait.
 */
trait DeveloperAppControllerTestTrait
{
    /** @var string Developer id. */
    protected static $developerId;

    /** @var string API Product name. */
    protected static $apiProductName;

    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass(): void
    {
        try {
            // Create required entities for testing this controller on Edge.
            // Unfortunately in PHPUnit it is not possible to directly depend on another test classes. This could ensure
            // that even if only a single test case is executed from this class those dependencies are executed first
            // and they could create these entities on Edge.
            $dc = new DeveloperController(static::getOrganization(static::$client), static::$client);
            try {
                // We have to keep a copy of phpunit@example.com developer's data because of this for offline tests.
                // See: offline-test-data/v1/organizations/phpunit/developers/phpunit@example.com .
                $entity = $dc->load(DeveloperControllerTest::sampleDataForEntityCreate()->getEmail());
                static::$developerId = $entity->id();
            } catch (ClientErrorException $e) {
                if ($e->getEdgeErrorCode() && 'developer.service.DeveloperDoesNotExist' === $e->getEdgeErrorCode()) {
                    $entity = clone DeveloperControllerTest::sampleDataForEntityCreate();
                    $dc->create($entity);
                    static::$developerId = $entity->id();
                }
            }

            $apc = new ApiProductController(static::getOrganization(static::$client), static::$client);
            try {
                $entity = $apc->load(ApiProductControllerTest::sampleDataForEntityCreate()->id());
                static::$apiProductName = $entity->id();
            } catch (ClientErrorException $e) {
                if ($e->getEdgeErrorCode() && 'keymanagement.service.apiproduct_doesnot_exist' === $e->getEdgeErrorCode()) {
                    $entity = clone ApiProductControllerTest::sampleDataForEntityCreate();
                    $apc->create($entity);
                    static::$apiProductName = $entity->id();
                }
            }
        } catch (ApiException $e) {
            // Ensure that created test data always gets removed after an API call fails here.
            // (By default tearDownAfterClass() is not called if (any) exception occurred here.)
            static::tearDownAfterClass();
            throw $e;
        }
    }

    /**
     * @inheritdoc
     */
    public static function tearDownAfterClass(): void
    {
        if (TestClientFactory::isMockClient(static::$client)) {
            return;
        }

        if (null !== static::$developerId) {
            $dc = new DeveloperController(static::getOrganization(static::$client), static::$client);
            try {
                $dc->delete(static::$developerId);
            } catch (\Exception $e) {
                printf(
                    "Unable to delete developer entity with %s id.\n",
                    static::$developerId
                );
            }
        }

        if (null !== static::$apiProductName) {
            $apc = new ApiProductController(static::getOrganization(static::$client), static::$client);
            try {
                $apc->delete(static::$apiProductName);
            } catch (\Exception $e) {
                printf(
                    "Unable to delete api product entity with %s id.\n",
                    static::$apiProductName
                );
            }
        }
    }
}
