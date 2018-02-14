<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Api\Management\Entity;

use Apigee\Edge\Entity\CommonEntityPropertiesAwareTrait;
use Apigee\Edge\Entity\Entity;
use Apigee\Edge\Entity\Property\DisplayNamePropertyAwareTrait;
use Apigee\Edge\Entity\Property\EnvironmentsPropertyAwareTrait;
use Apigee\Edge\Entity\Property\NamePropertyAwareTrait;
use Apigee\Edge\Entity\Property\PropertiesPropertyAwareTrait;
use Apigee\Edge\Structure\PropertiesProperty;

/**
 * Describes an Organization entity.
 */
class Organization extends Entity implements OrganizationInterface
{
    use DisplayNamePropertyAwareTrait;
    use CommonEntityPropertiesAwareTrait;
    use EnvironmentsPropertyAwareTrait;
    use NamePropertyAwareTrait;
    use PropertiesPropertyAwareTrait;

    protected const TYPES = [
        'trial',
        'paid',
    ];

    /** @var string */
    protected $type;

    /**
     * Organization constructor.
     *
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        $this->properties = new PropertiesProperty();
        parent::__construct($values);
    }

    /**
     * @inheritdoc
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @inheritdoc
     */
    public function setType(string $type): void
    {
        if (!in_array($type, self::TYPES)) {
            throw new \InvalidArgumentException(sprintf('%s type is not a valid.', $type));
        }
        $this->type = $type;
    }

    /**
     * @inheritdoc
     */
    public function getTypes(): array
    {
        return self::TYPES;
    }

    /**
     * @inheritdoc
     */
    public function isCpsEnabled(): bool
    {
        return (bool) $this->getPropertyValue('features.isCpsEnabled');
    }
}
