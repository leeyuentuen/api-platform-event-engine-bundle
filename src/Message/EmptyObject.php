<?php

declare(strict_types=1);

namespace ADS\Bundle\ApiPlatformEventEngineBundle\Message;

use ADS\Bundle\EventEngineBundle\Type\Type;
use ADS\JsonImmutableObjects\JsonSchemaAwareRecordLogic;

class EmptyObject implements Type
{
    use JsonSchemaAwareRecordLogic;
}
