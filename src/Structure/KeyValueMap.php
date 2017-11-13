<?php

namespace Apigee\Edge\Structure;

/**
 * Class KeyValueMap.
 *
 * @package Apigee\Edge\Structure
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class KeyValueMap implements KeyValueMapInterface
{
    /** @var array */
    protected $values = [];

    /**
     * KeyValueMapAwareTrait constructor.
     * @param array $values
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
        return new \ArrayIterator($this->values);
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        $return = [];
        foreach ($this->values as $key => $value) {
            $return[] = (object)['name' => $key, 'value' => $value];
        }
        return $return;
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return json_encode($this, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
