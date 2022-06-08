<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\AuditLog;

use App\Model\AuditLog\Entity\TaskNameInterface;
use Webmozart\Assert\Assert;

class TaskName implements TaskNameInterface
{
    public const TASK_CREATE_CP_LOCATION = 'create_cp_location';
    public const TASK_RENAME_CP_LOCATION = 'rename_cp_location';
    public const TASK_REMOVE_CP_LOCATION = 'remove_cp_location';
    public const TASK_RENAME_CP_PACKAGE = 'rename_cp_package';
    public const TASK_REMOVE_CP_PACKAGE = 'remove_cp_package';
    public const TASK_CHANGE_SOLIDCP_PLANS_CP_PACKAGE = 'change_solidcp_plans_cp_package';
    public const TASK_CREATE_CP_PACKAGE_VIRTUAL_MACHINE = 'create_cp_package_virtual_machine';
    public const TASK_EDIT_CP_PACKAGE_VIRTUAL_MACHINE = 'edit_cp_package_virtual_machine';
    public const TASK_CREATE_CP_SOLIDCP_ENTERPRISE_DISPATCHER = 'create_cp_solidcp_enterprise_dispatcher';
    public const TASK_REMOVE_CP_SOLIDCP_ENTERPRISE_DISPATCHER = 'remove_cp_solidcp_enterprise_dispatcher';
    public const TASK_EDIT_CP_SOLIDCP_ENTERPRISE_DISPATCHER = 'edit_cp_solidcp_enterprise_dispatcher';
    public const TASK_DISABLE_CP_SOLIDCP_ENTERPRISE_DISPATCHER = 'disable_cp_solidcp_enterprise_dispatcher';
    public const TASK_ENABLE_CP_SOLIDCP_ENTERPRISE_DISPATCHER = 'enable_cp_solidcp_enterprise_dispatcher';
    public const TASK_CREATE_CP_SOLIDCP_SERVER = 'create_cp_solidcp_server';
    public const TASK_REMOVE_CP_SOLIDCP_SERVER = 'remove_cp_solidcp_server';
    public const TASK_EDIT_CP_SOLIDCP_SERVER = 'edit_cp_solidcp_server';
    public const TASK_DISABLE_CP_SOLIDCP_SERVER = 'disable_cp_solidcp_server';
    public const TASK_ENABLE_CP_SOLIDCP_SERVER = 'enable_cp_solidcp_server';
    public const TASK_CREATE_CP_SOLIDCP_HOSTING_SPACE = 'create_cp_solidcp_hosting_space';
    public const TASK_REMOVE_CP_SOLIDCP_HOSTING_SPACE = 'remove_cp_solidcp_hosting_space';
    public const TASK_EDIT_CP_SOLIDCP_HOSTING_SPACE = 'edit_cp_solidcp_hosting_space';
    public const TASK_CREATE_CP_SOLIDCP_HOSTING_PLAN = 'create_cp_solidcp_hosting_plan';
    public const TASK_REMOVE_CP_SOLIDCP_HOSTING_PLAN = 'remove_cp_solidcp_hosting_plan';
    public const TASK_ADD_OS_TEMPLATE_CP_SOLIDCP_HOSTING_SPACE = 'add_os_template_cp_solidcp_hosting_space';
    public const TASK_REMOVE_OS_TEMPLATE_CP_SOLIDCP_HOSTING_SPACE = 'remove_os_template_cp_solidcp_hosting_space';
    public const TASK_DISABLE_CP_SOLIDCP_HOSTING_SPACE = 'disable_cp_solidcp_hosting_space';
    public const TASK_ENABLE_CP_SOLIDCP_HOSTING_SPACE = 'enable_cp_solidcp_hosting_space';

    public const TASK_CREATE_SOLIDCP_ALL_IN_ONE_VPS = 'create_solidcp_all_in_one_vps';
    public const TASK_CREATE_SOLIDCP_USER = 'create_solidcp_user';
    public const TASK_CHECK_SOLIDCP_USER = 'check_solidcp_user';
    public const TASK_CHECK_SOLIDCP_VPS_AVAILABLE_SPACES = 'task_check_solidcp_vps_available_spaces';
    public const TASK_CHANGE_SOLIDCP_USER_VPS_STATE = 'task_change_solidcp_user_vps_state';
    public const TASK_CREATE_SOLIDCP_PACKAGE = 'create_solidcp_package';
    public const TASK_CHANGE_SOLIDCP_PACKAGE_STATUS = 'change_solidcp_package_status';
    public const TASK_CREATE_SOLIDCP_VPS = 'create_solidcp_vps';
    private string $name;

    public static function create(string $name): self
    {
        Assert::oneOf($name, [
            self::TASK_CREATE_CP_LOCATION,
            self::TASK_RENAME_CP_LOCATION,
            self::TASK_REMOVE_CP_LOCATION,
            self::TASK_RENAME_CP_PACKAGE,
            self::TASK_REMOVE_CP_PACKAGE,
            self::TASK_CHANGE_SOLIDCP_PLANS_CP_PACKAGE,
            self::TASK_CREATE_CP_PACKAGE_VIRTUAL_MACHINE,
            self::TASK_EDIT_CP_PACKAGE_VIRTUAL_MACHINE,
            self::TASK_CREATE_CP_SOLIDCP_ENTERPRISE_DISPATCHER,
            self::TASK_REMOVE_CP_SOLIDCP_ENTERPRISE_DISPATCHER,
            self::TASK_EDIT_CP_SOLIDCP_ENTERPRISE_DISPATCHER,
            self::TASK_DISABLE_CP_SOLIDCP_ENTERPRISE_DISPATCHER,
            self::TASK_ENABLE_CP_SOLIDCP_ENTERPRISE_DISPATCHER,
            self::TASK_CREATE_CP_SOLIDCP_SERVER,
            self::TASK_REMOVE_CP_SOLIDCP_SERVER,
            self::TASK_EDIT_CP_SOLIDCP_SERVER,
            self::TASK_DISABLE_CP_SOLIDCP_SERVER,
            self::TASK_ENABLE_CP_SOLIDCP_SERVER,
            self::TASK_CREATE_CP_SOLIDCP_HOSTING_SPACE,
            self::TASK_REMOVE_CP_SOLIDCP_HOSTING_SPACE,
            self::TASK_EDIT_CP_SOLIDCP_HOSTING_SPACE,
            self::TASK_CREATE_CP_SOLIDCP_HOSTING_PLAN,
            self::TASK_REMOVE_CP_SOLIDCP_HOSTING_PLAN,
            self::TASK_ADD_OS_TEMPLATE_CP_SOLIDCP_HOSTING_SPACE,
            self::TASK_REMOVE_OS_TEMPLATE_CP_SOLIDCP_HOSTING_SPACE,
            self::TASK_DISABLE_CP_SOLIDCP_HOSTING_SPACE,
            self::TASK_ENABLE_CP_SOLIDCP_HOSTING_SPACE,

            self::TASK_CREATE_SOLIDCP_ALL_IN_ONE_VPS,
            self::TASK_CREATE_SOLIDCP_USER,
            self::TASK_CHECK_SOLIDCP_USER,
            self::TASK_CHECK_SOLIDCP_VPS_AVAILABLE_SPACES,
            self::TASK_CHANGE_SOLIDCP_USER_VPS_STATE,
            self::TASK_CREATE_SOLIDCP_PACKAGE,
            self::TASK_CHANGE_SOLIDCP_PACKAGE_STATUS,
            self::TASK_CREATE_SOLIDCP_VPS,
        ]);

        $entityType = new self();
        $entityType->name = $name;
        return $entityType;
    }

    public static function createCpLocation(): self
    {
        return self::create(self::TASK_CREATE_CP_LOCATION);
    }

    public static function renameCpLocation(): self
    {
        return self::create(self::TASK_RENAME_CP_LOCATION);
    }

    public static function removeCpLocation(): self
    {
        return self::create(self::TASK_REMOVE_CP_LOCATION);
    }

    public static function renameCpPackage(): self
    {
        return self::create(self::TASK_RENAME_CP_PACKAGE);
    }

    public static function removeCpPackage(): self
    {
        return self::create(self::TASK_REMOVE_CP_PACKAGE);
    }

    public static function changeSolidCpPlansCpPackage(): self
    {
        return self::create(self::TASK_CHANGE_SOLIDCP_PLANS_CP_PACKAGE);
    }

    public static function createCpPackageVirtualMachine(): self
    {
        return self::create(self::TASK_CREATE_CP_PACKAGE_VIRTUAL_MACHINE);
    }

    public static function editCpPackageVirtualMachine(): self
    {
        return self::create(self::TASK_EDIT_CP_PACKAGE_VIRTUAL_MACHINE);
    }

    public static function createCpSolidcpEnterpriseDispatcher(): self
    {
        return self::create(self::TASK_CREATE_CP_SOLIDCP_ENTERPRISE_DISPATCHER);
    }

    public static function removeCpSolidcpEnterpriseDispatcher(): self
    {
        return self::create(self::TASK_REMOVE_CP_SOLIDCP_ENTERPRISE_DISPATCHER);
    }

    public static function editCpSolidcpEnterpriseDispatcher(): self
    {
        return self::create(self::TASK_EDIT_CP_SOLIDCP_ENTERPRISE_DISPATCHER);
    }

    public static function disableCpSolidcpEnterpriseDispatcher(): self
    {
        return self::create(self::TASK_DISABLE_CP_SOLIDCP_ENTERPRISE_DISPATCHER);
    }

    public static function enableCpSolidcpEnterpriseDispatcher(): self
    {
        return self::create(self::TASK_ENABLE_CP_SOLIDCP_ENTERPRISE_DISPATCHER);
    }

    public static function createCpSolidcpServer(): self
    {
        return self::create(self::TASK_CREATE_CP_SOLIDCP_SERVER);
    }

    public static function removeCpSolidcpServer(): self
    {
        return self::create(self::TASK_REMOVE_CP_SOLIDCP_SERVER);
    }

    public static function editCpSolidcpServer(): self
    {
        return self::create(self::TASK_EDIT_CP_SOLIDCP_SERVER);
    }

    public static function disableCpSolidcpServer(): self
    {
        return self::create(self::TASK_DISABLE_CP_SOLIDCP_SERVER);
    }

    public static function enableCpSolidcpServer(): self
    {
        return self::create(self::TASK_ENABLE_CP_SOLIDCP_SERVER);
    }

    public static function createCpSolidcpHostingSpace(): self
    {
        return self::create(self::TASK_CREATE_CP_SOLIDCP_HOSTING_SPACE);
    }

    public static function removeCpSolidcpHostingSpace(): self
    {
        return self::create(self::TASK_REMOVE_CP_SOLIDCP_HOSTING_SPACE);
    }

    public static function editCpSolidcpHostingSpace(): self
    {
        return self::create(self::TASK_EDIT_CP_SOLIDCP_HOSTING_SPACE);
    }

    public static function createCpSolidcpHostingPlan(): self
    {
        return self::create(self::TASK_CREATE_CP_SOLIDCP_HOSTING_PLAN);
    }

    public static function removeCpSolidcpHostingPlan(): self
    {
        return self::create(self::TASK_REMOVE_CP_SOLIDCP_HOSTING_PLAN);
    }

    public static function addOsTemplateCpSolidcpHostingSpace(): self
    {
        return self::create(self::TASK_ADD_OS_TEMPLATE_CP_SOLIDCP_HOSTING_SPACE);
    }

    public static function removeOsTemplateCpSolidcpHostingSpace(): self
    {
        return self::create(self::TASK_REMOVE_OS_TEMPLATE_CP_SOLIDCP_HOSTING_SPACE);
    }

    public static function disableCpSolidcpHostingSpace(): self
    {
        return self::create(self::TASK_DISABLE_CP_SOLIDCP_HOSTING_SPACE);
    }

    public static function enableCpSolidcpHostingSpace(): self
    {
        return self::create(self::TASK_ENABLE_CP_SOLIDCP_HOSTING_SPACE);
    }

    public static function createSolidcpAllInOneVps(): self
    {
        return self::create(self::TASK_CREATE_SOLIDCP_ALL_IN_ONE_VPS);
    }

    public static function createSolidcpUser(): self
    {
        return self::create(self::TASK_CREATE_SOLIDCP_USER);
    }

    public static function checkSolidcpUser(): self
    {
        return self::create(self::TASK_CHECK_SOLIDCP_USER);
    }

    public static function checkSolidcpVpsAvailableSpaces(): self
    {
        return self::create(self::TASK_CHECK_SOLIDCP_VPS_AVAILABLE_SPACES);
    }

    public static function changeSolidcpUserVpsState(): self
    {
        return self::create(self::TASK_CHANGE_SOLIDCP_USER_VPS_STATE);
    }

    public static function createSolidcpPackage(): self
    {
        return self::create(self::TASK_CREATE_SOLIDCP_PACKAGE);
    }

    public static function changeSolidcpPackageStatus(): self
    {
        return self::create(self::TASK_CHANGE_SOLIDCP_PACKAGE_STATUS);
    }

    public static function createSolidcpVps(): self
    {
        return self::create(self::TASK_CREATE_SOLIDCP_VPS);
    }

    public function isEqual(TaskNameInterface $type): bool
    {
        return $this->getName() === $type->getName();
    }

    public function getName(): string
    {
        return $this->name;
    }
}