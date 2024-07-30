<?php

namespace Siarko\Serialization\Api\Json;

interface SerializableInterface
{

    /**
     * Serialize object to json
     * @return mixed primitive data structure
     */
    public function serialize(): mixed;

    /**
     * Deserialize object from json and return new instance
     * @param array $data
     * @return static
     */
    public static function deserialize(array $data): static;

}