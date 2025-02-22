<?php
declare(strict_types=1);

namespace App\Twig\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final readonly class NavbarMenu
{
    public function __construct(
        private FactoryInterface              $factory,
        private AuthorizationCheckerInterface $auth
    ) {}

    public function build(): ItemInterface
    {
        $menu = $this->factory->createItem('root')
            ->setChildrenAttributes(['class' => 'navbar-nav mr-auto']);

        if ($this->auth->isGranted('ROLE_MODERATOR')){
            $menu->addChild('Locations', ['route' => 'locations'])
                ->setExtra('routes', [
                    ['route' => 'locations'],
                    ['pattern' => '/^locations\..+/'],
                ])
                ->setAttribute('class', 'nav-item')
                ->setLinkAttribute('class', 'nav-link');

            $menu->addChild('VM Packages', ['route' => 'virtualMachinePackages'])
                //->setExtra('icon', 'sidebar-nav-icon icon-speedometer')
                ->setExtra('routes', [
                    ['route' => 'virtualMachinePackages'],
                    ['pattern' => '/^virtualMachinePackages\..+/'],
                ])
                ->setAttribute('class', 'nav-item')
                ->setLinkAttribute('class', 'nav-link');

            $menu->addChild('Enterprise Dispatchers', ['route' => 'enterpriseDispatchers'])
                ->setExtra('routes', [
                    ['route' => 'enterpriseDispatchers'],
                    ['pattern' => '/^enterpriseDispatchers\..+/'],
                ])
                ->setAttribute('class', 'nav-item')
                ->setLinkAttribute('class', 'nav-link');

            $menu->addChild('Node Servers', ['route' => 'solidCpServers'])
                ->setExtra('routes', [
                    ['route' => 'solidCpServers'],
                    ['pattern' => '/^solidCpServers\..+/'],
                ])
                ->setAttribute('class', 'nav-item')
                ->setLinkAttribute('class', 'nav-link');

            $menu->addChild('Hosting Spaces', ['route' => 'solidCpHostingSpaces'])
                ->setExtra('routes', [
                    ['route' => 'solidCpHostingSpaces'],
                    ['pattern' => '/^solidCpHostingSpaces\..+/'],
                ])
                ->setAttribute('class', 'nav-item')
                ->setLinkAttribute('class', 'nav-link');
        }

        if ($this->auth->isGranted('ROLE_ADMIN')) {
            $menu->addChild('Users', ['route' => 'users'])
                ->setExtra('routes', [
                    ['route' => 'users'],
                    ['pattern' => '/^users\..+/'],
                ])
                ->setAttribute('class', 'nav-item')
                ->setLinkAttribute('class', 'nav-link');

            $menu->addChild('Logs', ['route' => 'auditLogs'])
                ->setExtra('routes', [
                    ['route' => 'auditLogs'],
                    ['pattern' => '/^auditLogs\..+/'],
                ])
                ->setAttribute('class', 'nav-item')
                ->setLinkAttribute('class', 'nav-link');
        }

        return $menu;
    }
}