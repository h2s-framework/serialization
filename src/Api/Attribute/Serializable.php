<?php

namespace Siarko\Serialization\Api\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class Serializable
{

}