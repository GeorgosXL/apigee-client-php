<?php

namespace Apigee\Edge\Entity\Property;

/**
 * Trait StatusPropertyAwareTrait.
 *
 * @package Apigee\Edge\Entity\Property
 * @author Dezső Biczó <mxr576@gmail.com>
 *
 * @see StatusPropertyInterface
 */
trait StatusPropertyAwareTrait
{
    /** @var string */
    protected $status = '';

    /**
     * @inheritdoc
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Set status of this entity from an Edge API response.
     *
     * The status of an entity can not be changed by modifying the value of this property. Read more about this in
     * the docBlock of StatusPropertyInterface.
     *
     * @param string $status
     *   Status of the entity.
     *
     * @see \Apigee\Edge\Entity\Property\StatusPropertyInterface
     * @internal
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }
}
