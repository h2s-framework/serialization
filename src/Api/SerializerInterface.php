<?php

namespace Siarko\Serialization\Api;

/**
 * Interface for serialization
 */
interface SerializerInterface
{

    /**
     * Serialize data to string
     *
     * @param mixed $data
     * @return string
     */
    public function serialize(mixed $data): string;

    /**
     * Deserialize data from string
     *
     * @param string $data
     * @return mixed
     */
    public function deserialize(string $data): mixed;
}