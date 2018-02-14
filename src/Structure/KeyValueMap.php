<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Structure;

/**
 * Class KeyValueMap.
 */
abstract class KeyValueMap implements KeyValueMapInterface
{
    /** @var array */
    protected $values = [];

    /**
     * KeyValueMapAwareTrait constructor.
     *
     * @param array $values
     *   Associative array. Ex.: ['foo' => 'bar']
     */
    public function __construct(array $values = [])
    {
        $this->values = $values;
    }

    /**
     * @inheritdoc
     */
    public function values(): array
    {
        return $this->values;
    }

    /**
     * @inheritdoc
     */
    public function getValue(string $key): ?string
    {
        if (array_key_exists($key, $this->values())) {
            return $this->values[$key];
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function add(string $key, $value): void
    {
        $this->values[$key] = $value;
    }

    /**
     * @inheritdoc
     */
    public function set(array $values): void
    {
        $this->values = $values;
    }

    /**
     * @inheritdoc
     */
    public function delete(string $key): void
    {
        unset($this->values[$key]);
    }

    /**
     * @inheritdoc
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->values);
    }

    /**
     * @inheritdoc
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->values());
    }
}
