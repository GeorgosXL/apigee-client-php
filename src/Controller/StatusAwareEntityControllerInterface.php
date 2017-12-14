<?php

namespace Apigee\Edge\Controller;

use Apigee\Edge\Entity\Property\StatusPropertyAwareTrait;

/**
 * Interface StatusAwareEntityControllerInterface.
 *
 * Entity controller for those entities that has "status" property and the value of that property (and with that the
 * status of the entity itself) can be changed only with an additional API call.
 *
 * @see https://docs.apigee.com/management/apis/post/organizations/%7Borg_name%7D/developers/%7Bdeveloper_email_or_id%7D
 * @see https://docs.apigee.com/management/apis/post/organizations/%7Borg_name%7D/developers/%7Bdeveloper_email_or_id%7D/apps/%7Bapp_name%7D
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 *
 * @see StatusPropertyAwareTrait
 */
interface StatusAwareEntityControllerInterface
{
    public function setStatus(string $entityId, string $status): void;
}
