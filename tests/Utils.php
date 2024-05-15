<?php
declare(strict_types=1);

namespace App\Tests;

use Doctrine\Persistence\ObjectManager;

final class Utils
{
    /**
     * WARNING! ----> ONLY USE FOR FUNCTIONAL FIXTURES! <---- WARNING!
     *
     * Flushes the changes to a managed entity with a custom ID, ensuring that the entity is persisted with the provided custom ID.
     *
     * This function is particularly useful when you want to flush changes to an entity that has a manually assigned (custom) identifier.
     * It temporarily changes the ID generator for the entity's class to an assigned generator, flushes the changes to the database, and then restores the original ID generator.
     *
     * Original: https://stackoverflow.com/a/39034968/10142018
     */
    public static function flushEntityWithCustomId(ObjectManager $em, string $className): void
    {
        if (!class_exists($className)) {
            throw new \Exception("This $className does not exist");
        }

        $metadata = $em->getClassMetadata($className);
        $generator = $metadata->idGenerator;
        $generatorType = $metadata->generatorType;

        $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadataInfo::GENERATOR_TYPE_NONE);

        $unitOfWork = $em->getUnitOfWork();
        $persistersRef = new \ReflectionProperty($unitOfWork, 'persisters');
        $persisters = $persistersRef->getValue($unitOfWork);
        unset($persisters[$className]);
        $persistersRef->setValue($unitOfWork, $persisters);

        $em->flush();

        $metadata->setIdGenerator($generator);
        $metadata->setIdGeneratorType($generatorType);

        $persisters = $persistersRef->getValue($unitOfWork);
        unset($persisters[$className]);
        $persistersRef->setValue($unitOfWork, $persisters);
    }
}