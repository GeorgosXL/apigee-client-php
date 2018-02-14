<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Api\Management\Entity;

use Apigee\Edge\Entity\CommonEntityPropertiesInterface;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Entity\Property\AttributesPropertyInterface;
use Apigee\Edge\Entity\Property\DescriptionPropertyInterface;
use Apigee\Edge\Entity\Property\DisplayNamePropertyInterface;
use Apigee\Edge\Entity\Property\NamePropertyInterface;
use Apigee\Edge\Entity\Property\ScopesPropertyInterface;
use Apigee\Edge\Entity\Property\StatusPropertyInterface;

/**
 * Interface AppInterface.
 */
interface AppInterface extends
    EntityInterface,
    AttributesPropertyInterface,
    CommonEntityPropertiesInterface,
    DescriptionPropertyInterface,
    DisplayNamePropertyInterface,
    NamePropertyInterface,
    ScopesPropertyInterface,
    StatusPropertyInterface
{
    /**
     * Get OAuth scopes.
     *
     * Scopes of app can not be modified on the entity level therefore we could not extend the ScopesPropertyInterface
     * here.
     *
     * @return string[]
     */
    public function getScopes(): array;

    /**
     * @return string
     */
    public function getAppFamily(): string;

    /**
     * @param string $appFamily
     */
    public function setAppFamily(string $appFamily): void;

    /**
     * @return string
     */
    public function getAppId(): ?string;

    /**
     * @return string
     */
    public function getCallbackUrl(): ?string;

    /**
     * @param string $callbackUrl
     */
    public function setCallbackUrl(string $callbackUrl): void;

    /**
     * @return \Apigee\Edge\Api\Management\Entity\AppCredential[]
     */
    public function getCredentials(): array;
}
