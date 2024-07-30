<?php

namespace Siarko\Serialization;

abstract class SerializerBase
{

    /**
     * @var string
     */
    protected string $classTypeKey = '';

    /**
     * @var string
     */
    protected string $enumValueKey = '';
    /**
     * @var string
     */
    protected string $dataKey = '';

    /**
     * @return string
     */
    public function getClassTypeKey(): string
    {
        return $this->classTypeKey;
    }

    /**
     * @param string $classTypeKey
     * @return SerializerBase
     */
    public function setClassTypeKey(string $classTypeKey): static
    {
        $this->classTypeKey = $classTypeKey;
        return $this;
    }

    /**
     * @return string
     */
    public function getDataKey(): string
    {
        return $this->dataKey;
    }

    /**
     * @param string $dataKey
     * @return SerializerBase
     */
    public function setDataKey(string $dataKey): static
    {
        $this->dataKey = $dataKey;
        return $this;
    }

    /**
     * @return string
     */
    public function getEnumValueKey(): string
    {
        return $this->enumValueKey;
    }

    /**
     * @param string $enumValueKey
     * @return SerializerBase
     */
    public function setEnumValueKey(string $enumValueKey): static
    {
        $this->enumValueKey = $enumValueKey;
        return $this;
    }

}