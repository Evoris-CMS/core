<?php

namespace Evoris\Core\Normalizer;

use Evoris\Core\Atrribute\Page as PageAttribute;
use Evoris\Core\Page\Page;
use Evoris\Core\Page\PageInterface;
use Patchlevel\Hydrator\Normalizer\InvalidArgument;
use Patchlevel\Hydrator\Normalizer\Normalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[\Attribute(\Attribute::TARGET_CLASS|\Attribute::TARGET_PROPERTY)]
class PageNormalizer implements Normalizer
{
    private static array $pageClass = [];

    public function __construct(
        private readonly SerializerInterface $serializer
    ) {}

    public function normalize(mixed $value): string
    {
        if(!$value instanceof Page) {
            throw InvalidArgument::withWrongType(Page::class, $value);
        }

        $refl = new \ReflectionClass($value);

        $attributes = $refl->getAttributes(PageAttribute::class);

        if(count($attributes) === 0) {
            throw new InvalidArgument(sprintf('Page must have a attribute %s', PageAttribute::class));
        }

        $data = ['class' => $attributes[0]->name, 'data' => $this->serializer->serialize($value, 'json')];

        return $this->serializer->serialize($data, 'json');
    }

    public function denormalize(mixed $value): PageInterface|null
    {
        if($value === null) {
            return null;
        }

        $data = $this->serializer->deserialize($value, 'array', 'json');

        $object = $this->serializer->deserialize($data['data'], self::getPageClass($data['class']), 'json');

        if($object instanceof PageInterface) {
            return $object;
        }

        throw new InvalidArgument(sprintf('Class %s is not a page', $data['class']));

    }

    private static function getPageClasses(): array
    {
        if(count(self::$pageClass) !== 0) {
            return self::$pageClass;
        }

        foreach (get_declared_classes() as $class) {
            $reflection = new \ReflectionClass($class);

            if(!$reflection->implementsInterface(PageInterface::class)) {
                continue;
            }

            $attributes = $reflection->getAttributes(PageAttribute::class);
            if(count($attributes) === 0) {
                continue;
            }

            self::$pageClass[$attributes[0]->name] = $class;
        }

        return self::$pageClass;
    }

    private static function getPageClass(string $class): string
    {
        $pageClasses = self::getPageClasses();

        if(!array_key_exists($class, $pageClasses)) {
            throw new InvalidArgument(sprintf('Class %s is not a page', $class));
        }

        return $pageClasses[$class];
    }

}