<?php

namespace Siarko\Serialization\Json;


use Siarko\Serialization\Api\SerializerInterface;
use Siarko\Serialization\Deserializer;
use Siarko\Serialization\Serializer;

class JsonSerializer implements SerializerInterface
{
    /**
     * @param Serializer $serializer
     * @param Deserializer $deserializer
     * @param string $SERIALIZER_CLASS_TYPE_KEY
     * @param string $SERIALIZER_DATA_KEY
     * @param string $SERIALIZER_ENUM_VALUE_KEY
     */
    public function __construct(
        protected readonly Serializer $serializer,
        protected readonly Deserializer $deserializer,
        string $SERIALIZER_CLASS_TYPE_KEY = '__CLASS',
        string $SERIALIZER_DATA_KEY = '__DATA',
        string $SERIALIZER_ENUM_VALUE_KEY = '__ENUM'
    )
    {
        $this->serializer->setClassTypeKey($SERIALIZER_CLASS_TYPE_KEY);
        $this->serializer->setEnumValueKey($SERIALIZER_ENUM_VALUE_KEY);
        $this->serializer->setDataKey($SERIALIZER_DATA_KEY);
        $this->deserializer->setClassTypeKey($SERIALIZER_CLASS_TYPE_KEY);
        $this->deserializer->setEnumValueKey($SERIALIZER_ENUM_VALUE_KEY);
        $this->deserializer->setDataKey($SERIALIZER_DATA_KEY);
    }

    /**
     * @param $data
     * @return string
     * @throws \JsonException
     */
    public function serialize($data): string
    {
        return json_encode($this->serializer->prepareData($data), JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
    }

    /**
     * @param string $data
     * @return mixed
     * @throws \ReflectionException
     */
    public function deserialize(string $data): mixed
    {
        return $this->deserializer->deserialize(json_decode($data, true));
    }
}