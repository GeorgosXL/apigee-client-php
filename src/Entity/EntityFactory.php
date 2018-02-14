<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Entity;

use Apigee\Edge\Exception\EntityNotFoundException;

/**
 * Class EntityFactory.
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
        static::$classMappingCache[$className] = $fqcn;

        return static::$classMappingCache[$className];
    }

    /**
     * @inheritdoc
     */
    public function getEntityByController($entityController): EntityInterface
    {
        $className = $this->getClassName($entityController);
        $fqcn = $this->getEntityTypeByController($entityController);
        // Add it to to object cache.
        static::$objectCache[$className] = new $fqcn();

        return clone static::$objectCache[$className];
    }

    /**
     * Helper function that returns the FQCN of a class.
     *
     * @param string|\Apigee\Edge\Controller\AbstractEntityController $entityController
     *   Fully qualified class name or an object.
     *
     * @return string
     *   Fully qualified class name.
     */
    private function getClassName($entityController): string
    {
        return is_object($entityController) ? get_class($entityController) : $entityController;
    }
}
