<?php

declare(strict_types=1);

namespace ADS\Bundle\ApiPlatformEventEngineBundle\Message;

use ADS\Bundle\EventEngineBundle\Response\HasResponses;

interface AuthorizedApiPlatformWithResponsesMessage extends AuthorizedApiPlatformMessage, HasResponses
{
}
