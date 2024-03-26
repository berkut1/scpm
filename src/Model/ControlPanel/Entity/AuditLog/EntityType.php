<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\AuditLog;

use App\Model\AuditLog\Entity\EntityTypeInterface;
use Webmozart\Assert\Assert;

final class EntityType implements EntityTypeInterface
{
    public const string ENTITY_CP_LOCATION = 'cp_location';
    public const string ENTITY_CP_PACKAGE = 'cp_package';
    public const string ENTITY_CP_PACKAGE_VIRTUAL_MACHINE = 'cp_package_virtual_machine';
    public const string ENTITY_CP_SOLIDCP_ENTERPRISE_DISPATCHER = 'cp_solidcp_enterprise_dispatcher';
    public const string ENTITY_CP_SOLIDCP_SERVER = 'cp_solidcp_server';
    public const string ENTITY_CP_SOLIDCP_HOSTING_SPACE = 'cp_solidcp_hosting_space';
    public const string ENTITY_CP_SOLIDCP_HOSTING_PLAN = 'cp_solidcp_hosting_plan';

    public const string ENTITY_SOAP_EXECUTE = 'SOAP_EXECUTE';
    private string $name;

    #[\Override]
    public static function create(string $name): self
    {
        Assert::oneOf($name, [
            self::ENTITY_CP_LOCATION,
            self::ENTITY_CP_PACKAGE,
            self::ENTITY_CP_PACKAGE_VIRTUAL_MACHINE,
            self::ENTITY_CP_SOLIDCP_ENTERPRISE_DISPATCHER,
            self::ENTITY_CP_SOLIDCP_SERVER,
            self::ENTITY_CP_SOLIDCP_HOSTING_SPACE,
            self::ENTITY_CP_SOLIDCP_HOSTING_PLAN,
            self::ENTITY_SOAP_EXECUTE,
        ]);

        $entityType = new self();
        $entityType->name = $name;
        return $entityType;
    }

    public static function cpLocation(): self
    {
        return self::create(self::ENTITY_CP_LOCATION);
    }

    public static function cpPackage(): self
    {
        return self::create(self::ENTITY_CP_PACKAGE);
    }

    public static function cpPackageVirtualMachine(): self
    {
        return self::create(self::ENTITY_CP_PACKAGE_VIRTUAL_MACHINE);
    }

    public static function cpSolidcpEnterpriseDispatcher(): self
    {
        return self::create(self::ENTITY_CP_SOLIDCP_ENTERPRISE_DISPATCHER);
    }

    public static function cpSolidcpServer(): self
    {
        return self::create(self::ENTITY_CP_SOLIDCP_SERVER);
    }

    public static function cpSolidcpHostingSpace(): self
    {
        return self::create(self::ENTITY_CP_SOLIDCP_HOSTING_SPACE);
    }

    public static function cpSolidcpHostingPlan(): self
    {
        return self::create(self::ENTITY_CP_SOLIDCP_HOSTING_PLAN);
    }

    public static function soapExecute(): self
    {
        return self::create(self::ENTITY_SOAP_EXECUTE);
    }

    #[\Override]
    public function isEqual(EntityTypeInterface $type): bool
    {
        return $this->getName() === $type->getName();
    }

    #[\Override]
    public function getName(): string
    {
        return $this->name;
    }
}