<?php

declare(strict_types=1);

namespace ADS\Bundle\ApiPlatformEventEngineBundle\LinkFactory;

use ADS\Bundle\ApiPlatformEventEngineBundle\Message\ApiPlatformMessage;
use ADS\Bundle\ApiPlatformEventEngineBundle\ValueObject\Uri;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Resource\Factory\LinkFactoryInterface;

use function array_map;
use function class_implements;
use function in_array;

class StateLinkFactory implements LinkFactoryInterface
{
    public function __construct(private readonly LinkFactoryInterface $linkFactory)
    {
    }

    /**
     * @return array<Link>
     */
    public function createLinksFromIdentifiers(ApiResource|Operation $operation): array
    {
        if ($operation instanceof ApiResource || ! isset($operation->getInput()['class'])) {
            return $this->linkFactory->createLinksFromIdentifiers($operation);
        }

        $messageClass = $operation->getInput()['class'];
        $interfaces = class_implements($messageClass);

        if ($interfaces === false || ! in_array(ApiPlatformMessage::class, $interfaces)) {
            return $this->linkFactory->createLinksFromIdentifiers($operation);
        }

        $uri = Uri::fromString($messageClass::__uriTemplate());
        $uriVariables = $uri->toPathParameterNames();

        return array_map(
            static fn (string $uriVariable) => new Link(
                parameterName: $uriVariable,
                identifiers: [$uriVariable],
                fromClass: $messageClass,
                fromProperty: $uriVariable,
            ),
            $uriVariables
        );
    }

    /**
     * @return array<Link>
     */
    public function createLinksFromRelations(ApiResource|Operation $operation): array
    {
        return $this->linkFactory->createLinksFromRelations($operation);
    }

    /**
     * @return array<Link>
     */
    public function createLinksFromAttributes(ApiResource|Operation $operation): array
    {
        return $this->linkFactory->createLinksFromAttributes($operation);
    }

    public function completeLink(Link $link): Link
    {
        return $this->linkFactory->completeLink($link);
    }
}
