<?php

namespace Apigee\Edge\Api\Management\Controller;

use Apigee\Edge\Exception\ClientErrorException;
use Apigee\Edge\Structure\AttributesProperty;

/**
 * Interface AttributesAwareEntityControllerInterface.
 *
 * Contains reusable methods for those entities' controllers that supports attributes and attributes can be
 * modified on dedicated endpoints. Example: Developers, API products, etc.
 *
 * @link https://docs.apigee.com/api/developers-0
 * @link https://docs.apigee.com/api/api-products-1
 *
 * @package Apigee\Edge\Api\Management\Controller
 * @author Dezső Biczó <mxr576@gmail.com>
 * @see AttributesAwareEntityControllerTrait
 */
interface AttributesAwareEntityControllerInterface
{
    /**
     * Returns a list of all entity attributes.
     *
     * @param string $entityId
     * @return AttributesProperty
     */
    public function getAttributes(string $entityId): AttributesProperty;

    /**
     * Returns the value of an entity attribute.
     *
     * @param string $entityId
     * @param string $name
     * @return string
     */
    public function getAttribute(string $entityId, string $name): string;

    /**
     * Updates or creates developer attributes.
     *
     * This API replaces the current list of attributes with the attributes specified in the request body.
     * This lets you update existing attributes, add new attributes, or delete existing attributes by omitting them
     * from the request body.
     *
     * @param string $entityId
     * @param AttributesProperty $attributes
     * @return string
     */
    public function updateAttributes(string $entityId, AttributesProperty $attributes): string;

    /**
     * Updates the value of an entity attribute.
     *
     * @param string $entityId
     * @param string $name
     * @param string $value
     * @return string
     * @throws ClientErrorException If attribute does not exist.
     */
    public function updateAttribute(string $entityId, string $name, string $value): string;

    /**
     * Deletes an entity attribute.
     *
     * @param string $entityId
     * @param string $name
     *
     * @throws ClientErrorException If attribute does not exist.
     */
    public function deleteAttribute(string $entityId, string $name): void;
}
