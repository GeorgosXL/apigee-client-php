<?php

namespace Apigee\Edge\Tests\Test\Controller;

/**
 * Class NonCpsLimitEntityControllerValidator.
 *
 * Helps in validation of all entity controllers that implements NonCpsLimitEntityControllerInterface.
 *
 * @package Apigee\Edge\Tests\Test\Controller
 * @author Dezső Biczó <mxr576@gmail.com>
 * @see \Apigee\Edge\Entity\NonCpsListingEntityControllerInterface
 */
abstract class NonCpsLimitEntityControllerValidator extends EntityCrudOperationsControllerValidator
{
    /**
     * @depends testCreate
     */
    public function testGetEntityIds()
    {
        /** @var \Apigee\Edge\Entity\NonCpsListingEntityControllerInterface $controller */
        $controller = $this->getEntityController();
        $this->assertNotEmpty($controller->getEntityIds());
    }
}
