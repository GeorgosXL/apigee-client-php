<?php

namespace Apigee\Edge\Tests\Test\Controller;

/**
 * Class NonCpsLimitEntityControllerValidator.
 *
 * Helps in validation of all entity controllers that implements NonCpsLimitEntityControllerInterface.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 *
 * @see \Apigee\Edge\Entity\NonCpsListingEntityControllerInterface
 */
abstract class NonCpsLimitEntityControllerValidator extends EntityCrudOperationsControllerValidator
{
    /**
     * @depends testCreate
     */
    public function testGetEntityIds(): void
    {
        /** @var \Apigee\Edge\Controller\NonCpsListingEntityControllerInterface $controller */
        $controller = $this->getEntityController();
        $this->assertNotEmpty($controller->getEntityIds());
    }

    /**
     * @depends testCreate
     */
    public function testGetEntities(): void
    {
        /** @var \Apigee\Edge\Controller\NonCpsListingEntityControllerInterface $controller */
        $controller = $this->getEntityController();
        $entities = $controller->getEntities();
        $this->assertNotEmpty($entities);
        /** @var \Apigee\Edge\Entity\EntityInterface $entity */
        foreach ($entities as $entity) {
            $this->assertEntityHasAllPropertiesSet($entity);
            if ($entity->id() == static::expectedAfterEntityCreate()->id()) {
                $this->assertArraySubset(
                    array_filter(static::$objectNormalizer->normalize(static::expectedAfterEntityCreate())),
                    static::$objectNormalizer->normalize($entity)
                );
            }
        }
    }
}
