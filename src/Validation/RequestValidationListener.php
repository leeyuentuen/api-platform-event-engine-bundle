<?php

declare(strict_types=1);

namespace ADS\Bundle\ApiPlatformEventEngineBundle\Validation;

use ADS\Bundle\ApiPlatformEventEngineBundle\Serializer\CustomContextBuilder;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Symfony\EventListener\EventPriorities;
use ApiPlatform\Util\RequestAttributesExtractor;
use ApiPlatform\Validator\ValidatorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class RequestValidationListener implements EventSubscriberInterface
{
    public function __construct(
        private ValidatorInterface $validator,
        private ResourceMetadataCollectionFactoryInterface $resourceMetadataCollectionFactory
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['validateMessage', EventPriorities::PRE_READ],
        ];
    }

    public function validateMessage(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $attributes = RequestAttributesExtractor::extractAttributes($request);
        $method = $request->getMethod();

        if (
            ($method !== Request::METHOD_DELETE && ! $request->isMethodSafe())
            || ! $attributes
            || ! $attributes['receive']
            || ! isset($attributes['resource_class'])
        ) {
            return;
        }

        $message = CustomContextBuilder::messageFromRequest($request);

        if ($message === null) {
            return;
        }

        $resourceMetadataCollection = $this->resourceMetadataCollectionFactory->create($attributes['resource_class']);

        foreach ($resourceMetadataCollection as $resourceMetadata) {
            $this->validator->validate($message, ['groups' => $resourceMetadata->getValidationContext()]);
        }
    }
}
