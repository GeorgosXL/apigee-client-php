<?php

namespace Apigee\Edge\Entity\Property;

use Apigee\Edge\Structure\AttributesProperty;

/**
 * Trait AttributesPropertyAwareTrait.
 *
 * @package Apigee\Edge\Entity\Property
 * @author Dezső Biczó <mxr576@gmail.com>
 * @see AttributesPropertyInterface
 */
trait AttributesPropertyAwareTrait
{
    /** @var AttributesProperty */
    protected $attributes;

    /**
     * @inheritdoc
     */
    public function getAttributes(): AttributesProperty
    {
        return $this->attributes;
    }

    /**
     * @inheritdoc
     */
    public function setAttributes(array $attributes): void
    {
        $this->attributes->set($attributes);
    }

    /**
     * @inheritdoc
     */
    public function getAttributeValue(string $attribute): ?string
    {
        return $this->attributes->getValue($attribute);
    }

    /**
     * @inheritdoc
     */
    public function addAttribute(string $name, string $value): void
    {
        $this->attributes->add($name, $value);
    }

    /**
     * @inheritdoc
     */
    public function hasAttribute(string $name): bool
    {
        return $this->attributes->has($name);
    }

    /**
     * @inheritdoc
     */
    public function deleteAttribute(string $name): void
    {
        $this->attributes->delete($name);
    }
}
