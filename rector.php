<?php
declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\Config\RectorConfig;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Php81\Rector\Property\ReadOnlyPropertyRector;
use Rector\Php82\Rector\Class_\ReadOnlyClassRector;
use Rector\PHPUnit\Rector\Class_\PreferPHPUnitSelfCallRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Symfony\CodeQuality\Rector\BinaryOp\ResponseStatusCodeRector;
use Rector\Symfony\CodeQuality\Rector\MethodCall\AssertSameResponseCodeWithDebugContentsRector;
use Rector\Symfony\CodeQuality\Rector\MethodCall\LiteralGetToRequestClassConstantRector;
use Rector\Symfony\Set\SymfonySetList;
use Rector\Symfony\Symfony40\Rector\MethodCall\FormIsValidRector;
use Rector\Symfony\Symfony61\Rector\Class_\CommandPropertyToAttributeRector;
use Rector\Symfony\Symfony62\Rector\ClassMethod\ParamConverterAttributeToMapEntityAttributeRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->symfonyContainerXml(__DIR__ . '/var/cache/dev/App_KernelDevDebugContainer.xml');
    $rectorConfig->paths([
        __DIR__ . '/bin',
        __DIR__ . '/config',
        __DIR__ . '/public',
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/translations',
    ]);

    // define sets of rules
    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_83,

        DoctrineSetList::DOCTRINE_DBAL_40,
        DoctrineSetList::DOCTRINE_ORM_214,
        DoctrineSetList::ANNOTATIONS_TO_ATTRIBUTES,
        DoctrineSetList::DOCTRINE_CODE_QUALITY,

        SymfonySetList::ANNOTATIONS_TO_ATTRIBUTES,
        SymfonySetList::SYMFONY_64,
        SymfonySetList::SYMFONY_CODE_QUALITY,
        SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION,

        PHPUnitSetList::PHPUNIT_100,
        PHPUnitSetList::PHPUNIT_CODE_QUALITY,
        PHPUnitSetList::ANNOTATIONS_TO_ATTRIBUTES,
    ]);

    $rectorConfig->skip([
        __DIR__ . '/src/Kernel.php',
        __DIR__ . '/src/EntityFromDB',
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
            __DIR__ . '/tests/Builder',
        ],
        ReadOnlyPropertyRector::class => [ //TODO: remove in future ?
            __DIR__ . '/src/Model/AuditLog/Entity',
            __DIR__ . '/src/Model/ControlPanel/Entity', //some readonly ids in constructors throw an error by doctrine/symfony
            __DIR__ . '/src/Model/User/Entity',
        ],
        ReadOnlyClassRector::class, //buggy
        AssertSameResponseCodeWithDebugContentsRector::class, //TODO: remove ? https://github.com/rectorphp/rector-symfony/blob/main/docs/rector_rules_overview.md#assertsameresponsecodewithdebugcontentsrector
        ResponseStatusCodeRector::class => [
            __DIR__ . '/tests',
        ],
        LiteralGetToRequestClassConstantRector::class => [
            __DIR__ . '/tests',
        ],
    ]);

    $rectorConfig->rules([
        PreferPHPUnitSelfCallRector::class,
        InlineConstructorDefaultToPropertyRector::class,
        CommandPropertyToAttributeRector::class,
        FormIsValidRector::class,
        ResponseStatusCodeRector::class,
        ParamConverterAttributeToMapEntityAttributeRector::class, //from Symfony 5.4 to 6.4
    ]);
};
