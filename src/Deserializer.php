<?php

namespace Siarko\Serialization;

use Siarko\Serialization\Api\Json\SerializableInterface;
use Siarko\Api\Factory\FactoryProviderInterface;

class Deserializer extends SerializerBase
{

    /**
     * @param FactoryProviderInterface $factoryProvider
     */
    public function __construct(
        protected readonly FactoryProviderInterface $factoryProvider
    )
    {
    }

    /**
     * @param $data
     * @return mixed
     * @throws \ReflectionException
     */
    public function deserialize($data): mixed
    {
        if(is_array($data)){
            return $this->deserializeArray($data);
        }else{
            return $this->deserializeEntity($data);
        }
    }

    /**
     * @param array $data
     * @return array|mixed|SerializableInterface
     * @throws \ReflectionException
     */
    protected function deserializeArray(array $data): mixed
    {
        if(array_key_exists($this->getClassTypeKey(), $data)){ //typed object
            return $this->deserializeObject($data);
        }else{ //normal array
            $result = [];
            foreach ($data as $key => $value) {
                $result[$key] = $this->deserialize($value);
            }
            return $result;
        }
    }

    /**
     * @param array $data
     * @return mixed|SerializableInterface
     * @throws \ReflectionException
     */
    protected function deserializeObject(array $data): mixed
    {
        $class = $data[$this->getClassTypeKey()];
        $fields = (array_key_exists($this->getDataKey(), $data) ? $data[$this->getDataKey()] : []);
        $reflection = new \ReflectionClass($class);
        if($reflection->isEnum()){
            return constant($class.'::'.$data[$this->getEnumValueKey()]);
        }
        return $this->createInstance($reflection, $fields);
    }

    /**
     * @param \ReflectionClass $reflection
     * @param array $fieldData
     * @return mixed
     * @throws \ReflectionException
     */
    protected function createInstance(\ReflectionClass $reflection, array $fieldData): mixed
    {
        if($reflection->implementsInterface(SerializableInterface::class)){
            /** @var SerializableInterface $className */
            $className = $reflection->getName();
            return $className::deserialize($fieldData);
        }
        $factory = $this->factoryProvider->getFactory($reflection->getName());
        $arguments = $this->createArgumentList($this->getConstructorParams($reflection), $fieldData);
        $instance = $factory->create($arguments);
        $this->executeSetters(
            $instance, $reflection,
            array_filter($fieldData, function($key) use ($arguments){
                return !array_key_exists($key, $arguments);
            },
                ARRAY_FILTER_USE_KEY
            )
        );

        return $instance;
    }

    /**
     * @param $instance
     * @param \ReflectionClass $reflectionClass
     * @param array $fields
     * @return void
     * @throws \ReflectionException
     */
    protected function executeSetters($instance, \ReflectionClass $reflectionClass, array $fields): void
    {
        foreach ($fields as $name => $value) {
            $method = "set".ucfirst($name);
            if($reflectionClass->hasMethod($method)){
                $instance->$method($this->deserialize($value));
            }
        }
    }

    /**
     * @param array $parameters
     * @param array $data
     * @return array
     * @throws \ReflectionException
     */
    protected function createArgumentList(array $parameters, array $data): array
    {
        $result = [];
        foreach ($parameters as $paramName => $defaultValue) {
            if(array_key_exists($paramName, $data)){
                $result[$paramName] = $this->deserialize($data[$paramName]);
            }
        }
        return $result;
    }

    /**
     * @param \ReflectionClass $reflectionClass
     * @return array
     */
    protected function getConstructorParams(\ReflectionClass $reflectionClass): array
    {
        $constructor = $reflectionClass->getConstructor();
        if($constructor == null){
            return [];
        }
        $result = [];
        foreach ($constructor->getParameters() as $parameter) {
            $result[$parameter->getName()] = ($parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue(): null);
        }
        return $result;
    }

    /**
     * @param $data
     * @return mixed
     */
    protected function deserializeEntity($data): mixed
    {
        return $data;
    }

}