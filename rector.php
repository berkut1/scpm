<?php
declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\Config\RectorConfig;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Php81\Rector\Property\ReadOnlyPropertyRector;
use Rector\Php82\Rector\Class_\ReadOnlyClassRector;
use Rector\Privatization\Rector\Class_\FinalizeClassesWithoutChildrenRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Symfony\CodeQuality\Rector\BinaryOp\ResponseStatusCodeRector;
use Rector\Symfony\Set\SensiolabsSetList;
use Rector\Symfony\Set\SymfonySetList;
use Rector\Symfony\Symfony40\Rector\MethodCall\FormIsValidRector;
use Rector\Symfony\Symfony61\Rector\Class_\CommandPropertyToAttributeRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->symfonyContainerXml(__DIR__ . '/var/cache/dev/App_KernelDevDebugContainer.xml');
    $rectorConfig->paths([
        __DIR__ . '/src',
    ]);
    $rectorConfig->skip([
        __DIR__ . '/src/Kernel.php',
        ClosureToArrowFunctionRector::class, //I don't like Arrow functions
        ClassPropertyAssignToConstructorPromotionRector::class => [ //not touch some constructors, because of mess after "refactoring" (two styles in one constructor look awful)
            __DIR__ . '/src/Model/AuditLog/Entity',
            __DIR__ . '/src/Model/ControlPanel/Entity',
            __DIR__ . '/src/Model/User/Entity',
            __DIR__ . '/src/Model/*/UseCase/*/*Command*.php',
            __DIR__ . '/src/ReadModel/AuditLog',
            __DIR__ . '/src/ReadModel/User',
            __DIR__ . '/src/ReadModel/ControlPanel',
            __DIR__ . '/src/Service',
        ],
        ReadOnlyPropertyRector::class => [ //remove in future ?
            __DIR__ . '/src/Model/AuditLog/Entity',
            __DIR__ . '/src/Model/ControlPanel/Entity', //some readonly ids in constructors throw an error by doctrine/symfony
            __DIR__ . '/src/Model/User/Entity',
        ],
        ReadOnlyClassRector::class, //buggy
    ]);

    // register a single rule
    $rectorConfig->rule(InlineConstructorDefaultToPropertyRector::class);
    $rectorConfig->rule(CommandPropertyToAttributeRector::class);
    $rectorConfig->rule(FormIsValidRector::class);
    $rectorConfig->rule(ResponseStatusCodeRector::class);

    // define sets of rules
    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_83,
        DoctrineSetList::ANNOTATIONS_TO_ATTRIBUTES,
        DoctrineSetList::DOCTRINE_CODE_QUALITY,
        SymfonySetList::ANNOTATIONS_TO_ATTRIBUTES,
        SymfonySetList::SYMFONY_54,
        SensiolabsSetList::ANNOTATIONS_TO_ATTRIBUTES,
        SymfonySetList::SYMFONY_CODE_QUALITY,
        SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION,
    ]);
};
