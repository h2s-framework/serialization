<?php

namespace Siarko\Serialization;

use ReflectionClass;
use Siarko\Serialization\Api\Attribute\Serializable;
use Siarko\Serialization\Api\Json\SerializableInterface;

class Serializer extends SerializerBase
{

    /**
     * @param $data
     * @return mixed
     */
    public function prepareData($data): mixed
    {
        if (is_array($data)) {
            return $this->prepareArray($data);
        } else {
            return $this->prepareEntity($data);
        }
    }

    /**
     * @param array $array
     * @return array
     */
    protected function prepareArray(array $array): array
    {
        $entitiesSerialized = [];
        foreach ($array as $k => $value) {
            $entitiesSerialized[$k] = $this->prepareData($value);
        }
        return $entitiesSerialized;
    }

    /**
     * @param $entity
     * @return mixed
     */
    protected function prepareEntity($entity): mixed
    {
        if (gettype($entity) == 'object') {
            return $this->prepareArray($this->prepareObject($entity));
        }
        return $entity;
    }

    /**
     * @param object $object
     * @return array
     */
    protected function prepareObject(object $object): array
    {
        $result = [$this->getClassTypeKey() => get_class($object)];
        if ($object instanceof SerializableInterface) {
            $result[$this->getDataKey()] = $object->serialize();
        } else {
            $reflection = new ReflectionClass($object);
            if ($reflection->isEnum()) {
                $result[$this->getEnumValueKey()] = $object->name;
            }else{
                $values = $this->prepareObjectByReflection($object, $reflection);
                if (count($values) > 0) {
                    $result[$this->getDataKey()] = $values;
                }
            }

        }
        return $result;
    }

    /**
     * @param object $object
     * @param ReflectionClass $reflectionClass
     * @return array
     */
    protected function prepareObjectByReflection(object $object, ReflectionClass $reflectionClass): array
    {
        $values = [];
        $class = $reflectionClass;
        do{
            foreach ($class->getProperties() as $property) {
                $attribute = current($property->getAttributes(Serializable::class));
                if ($attribute) {
                    $values[$property->getName()] = $property->getValue($object);
                }
            }
        }while(($class = $class->getParentClass()) != false);
        return $values;
    }

}