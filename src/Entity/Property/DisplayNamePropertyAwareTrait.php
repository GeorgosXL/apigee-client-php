<?php

namespace Apigee\Edge\Entity\Property;

/**
 * Trait DisplayNamePropertyAwareTrait.
 *
 * @package Apigee\Edge\Entity\Property
 * @author Dezső Biczó <mxr576@gmail.com>
 */
trait DisplayNamePropertyAwareTrait
{
    /** @var string */
    protected $displayName;

    /**
     * @inheritdoc
     */
    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    /**
     * @inheritdoc
     */
    public function setDisplayName(string $displayName): void
    {
        $this->displayName = $displayName;
    }
}
