<?php

namespace Apigee\Edge\Structure;

/**
 * Class Attributes.
 *
 * @package Apigee\Edge\Structure
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class AttributesProperty extends KeyValueMap
{
    /**
     * @inheritdoc
     */
    public function __construct(array $values = [])
    {
        $values = $this->transform($values);
        parent::__construct($values);
    }

    /**
     * @inheritdoc
     */
    public function set(array $values): void
    {
        $values = $this->transform($values);
        parent::set($values);
    }

    /**
     * Flatten the array returned by the Edge.
     *
     * Example: $values = [['name' => 'foo', 'value' => 'bar'], ['name' => 'bar', 'value' => 'baz']].
     *
     * @param array $values
     * @return array
     */
    protected function transform(array $values): array
    {
        $flatten = [];
        foreach ($values as $item) {
            $flatten[$item['name']] = $item['value'];
        }
        $values = $flatten;
        return $values;
    }
}
