<?php

namespace Apigee\Edge\Entity;

use Apigee\Edge\Exception\EntityNotFoundException;

/**
 * Class EntityFactory.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 */
final class EntityFactory implements EntityFactoryInterface
{
    /**
     * Stores mapping of entity classes by controllers.
     *
     * @var string[]
     */
    private static $classMappingCache = [];

    /**
     * Entity object cache.
     *
     * @var EntityInterface[]
     */
    private static $objectCache = [];

    /**
     * @inheritdoc
     */
    public function getEntityTypeByController($entityController): string
    {
        $className = $this->getClassName($entityController);
        // Try to find it in the static cache first.
        if (isset(self::$classMappingCache[$className])) {
            return self::$classMappingCache[$className];
        }
        $fqcn_parts = explode('\\', $className);
        $entityControllerClass = array_pop($fqcn_parts);
        // Special handling of DeveloperAppCredentialController and CompanyAppCredentialController entity controllers,
        // because those uses the same entity, AppCredential.
        $appCredentialController = 'AppCredentialController';
        $isAppCredentialController = (0 === substr_compare(
            $entityControllerClass,
            $appCredentialController,
            strlen($entityControllerClass) - strlen($appCredentialController),
            strlen($appCredentialController)
        ));
        if ($isAppCredentialController) {
            $entityControllerClass = $appCredentialController;
        }
        // Get rid of "Controller" from the namespace.
        array_pop($fqcn_parts);
        // Add "Entity" instead.
        $fqcn_parts[] = 'Entity';
        $entityControllerClassNameParts = preg_split('/(?=[A-Z])/', $entityControllerClass);
        // First index is an empty string, the last one is "Controller". Let's get rid of those.
        array_shift($entityControllerClassNameParts);
        array_pop($entityControllerClassNameParts);
        $fqcn_parts[] = implode('', $entityControllerClassNameParts);
        $fqcn = implode('\\', $fqcn_parts);
        if (!class_exists($fqcn)) {
            throw new EntityNotFoundException($fqcn);
        }
        // Add it to to object cache.
        self::$classMappingCache[$className] = $fqcn;

        return self::$classMappingCache[$className];
    }

    /**
     * @inheritdoc
     */
    public function getEntityByController($entityController): EntityInterface
    {
        $className = $this->getClassName($entityController);
        $fqcn = self::getEntityTypeByController($entityController);
        // Add it to to object cache.
        self::$objectCache[$className] = new $fqcn();

        return clone self::$objectCache[$className];
    }

    /**
     * Helper function that returns the FQCN of a class.
     *
     * @param string|\Apigee\Edge\Entity\AbstractEntityController $entityController
     *   Fully qualified class name or an object.
     *
     * @return string
     *   Fully qualified class name.
     */
    private function getClassName($entityController): string
    {
        $className = $entityController;
        if (is_object($entityController)) {
            $className = get_class($entityController);
        }

        return $className;
    }
}
