<?php

namespace Apigee\Edge\Entity;

/**
 * Interface BaseEntityControllerInterface.
 *
 * @package Apigee\Edge\Entity
 * @author Dezső Biczó <mxr576@gmail.com>
 */
interface BaseEntityControllerInterface
{
    /**
     * Loads an entity by ID from Edge.
     *
     * @param string $entityId
     *
     * @return EntityInterface
     */
    public function load(string $entityId): EntityInterface;

    /**
     * Creates an entity to Edge.
     *
     * @param EntityInterface $entity
     *
     * @return EntityInterface
     */
    public function create(EntityInterface $entity): EntityInterface;

    /**
     * Updates an entity to Edge.
     *
     * @param EntityInterface $entity
     *
     * @return EntityInterface
     */
    public function update(EntityInterface $entity): EntityInterface;

    /**
     * Removes an entity from Edge.
     *
     * @param string $entityId
     *
     * @return EntityInterface
     */
    public function delete(string $entityId): EntityInterface;
}
