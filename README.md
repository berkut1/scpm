[English](#EN) / [Russian](#RU)

EN
------------
SCPM is a simple panel manager that provide a simplify REST API over undocumented SOAP protocols from SolidCP (https://github.com/FuseCP/SolidCP).
For example, if you want to automate the creation of virtual machines, via your billing panel.

At the moment SCPM only supports creating __HyperV VMs__ via SolidCP SOAP.

Swagger API docs - http://localhost/docs/index.html

Requirements
------------
- PHP >= 8.3
- PostgresSQL
- SolidCP 1.4.9

Installation
------------
apache or nginx (https://symfony.com/doc/6.4/setup/web_server_configuration.html#nginx)
- `sudo apt install php8.3-soap php8.3-xml php8.3-mbstring php8.3-intl php8.3-curl` - php libs
- `git clone --branch builded https://github.com/berkut1/scpm.git`
- `composer install`
- `composer dump-env prod` after in the file .env.local.php update APP_SECRET, DATABASE_URL and JWT_PASSPHRASE variables
- `composer install --no-dev --optimize-autoloader`
- `php bin/console asset-map:compile`
- `APP_ENV=prod APP_DEBUG=0 php bin/console cache:clear`

More information here - https://symfony.com/doc/6.4/deployment.html
- `php bin/console lexik:jwt:generate-keypair` - generate jwt key
- `php bin/console doctrine:migrations:migrate` - create sql tables
- `php bin/console user:create` - create a panel user

Update
------------
- `git pull`
- `composer sefl-update`
- `composer install --no-dev --optimize-autoloader`
- `php bin/console asset-map:compile`
- `APP_ENV=prod APP_DEBUG=0 php bin/console cache:clear`

Descriptions (true for HyperV SolidCP module)
------------
1. Enterprise Dispatchers - SolidCP an Enterprise server with a reseller account. Normally you should have only one Enterprise 
server and one main reseller account, which contact with all SolidCP servers in different locations. Not recommended to use
serveradmin account (tested only with the reseller).
2. Node Servers - physical node/server, but can represent many "logical" SolidCP servers, 
but physically they are one server (The SolidCP "feature" of configuration, if you need to separate storages in the one node).
3. Hosting Spaces - SolidCP reseller Hosting Space and also represent SolidCP storages, where should be created a client VM.
In the Hosting Space you should assign all plans that should be use for VMs.
4. VM Packages - VM packages/product, the name of which you provide to clients through your billing panel.
   To the selected package, you need to assign all SolidCP plans that this package will use to create client servers.

How it works
------------
You send the VM package name and related data to the API -> the SCPM searches from assigned to the "VM" package possible plans (which are bound to the specific storages) ->
SCPM send to SoliCP request to create a user with a Hosting Space that represent one of selected reseller SolidCP plans -> after that
create a VM with information that has the VM package.

__SCMP creates a new hosting space for each Virtual Machine.__

User -> new Hosting Space -> new VM

In SolidCP, one client hosting space must have one Virtual Machine, because SolidCP can only suspend the hosting space, 
but not certain items in the hosting space.

Rest API call order example
-----------
1. Get Authentication token - /api/login/authentication_token
2. Create a VM /api/solidCP/all-in-one/user/package/vps
3. Wait until the provision status returns "OK" - /api/solidCP/vps/{solidcp_item_id}/provisioning-status
4. Check that the VM properly started - return status - "Running" /api/solidCP/vps/{solidcp_item_id}/state
5. The VM server is ready.

RU
------------
SCPM - это простая панель управления, которая предоставляет простой Rest API, поверх не документированного SOAP панели SolidCP (https://github.com/FuseCP/SolidCP).
Например, если вам нужно автоматизировать создание серверов для вашей биллинговой панели.

В данный момент, данное приложение поддерживает только создание Вирутальных Машин HyperV через SOAP вызовы панели SolidCP.

Swagger API docs - http://localhost/docs/index.html

Requirements
------------
- PHP >= 8.3
- PostgresSQL (не тестировалось с другими СУБД)

Описание (true for HyperV SolidCP module)
------------
1. Enterprise Dispatchers - SolidCP Enterprise сервер и аккаунт реселлера. Обычно у вас должен быть только один Enterprise
сервер и один главный аккаунт реселлера, которые связывают все SolidCP сервера в разных локациях. Не рекомендуется использовать
serveradmin аккаунт (тестировалось только с помощью реселлер аккаунта).
2. Node Servers - физический сервер/нода, но который может представлять множество "логических" SolidCP серверов, 
но физически это всё равно один сервер (У SolidCP "фишка" настройки, если вам нужно разделить места хранения на одном сервере,
вам нужно создать ещё один сервер, но указать другое место хранения).
3. Hosting Spaces - SolidCP Hosting Space реселлера и так же представляют собой диски/хранилище SolidCP, 
где будут размещаться клиентские сервера. В Hosting Space вы должны привязать все SolidCP планы, которые используются 
панелью для создание клиентских серверов.
4. VM Packages - пакеты/продукт серверов, название которых вы предоставляете клиентам через свои биллинг панели. 
К выбранному пакету нужно привязать все SolidCP планы, которые данный пакет будет использовать для создание клиенский серверов.

Логика работы данного приложения
------------
Вы отправляете название пакеты и сопутствующие данные в API -> SCPM ищет подходящий SolidCP план (который привязан к нужному хранилищу)
-> SCPM отправляет запрос к SolidCP на создание клиента и его Hosting Space, который является одним из выбранных SolidCP планов реселлера
-> после этого в новом Hosting Space создаётся клиентская виртуальная машина с данными, которые содержатся в VM Package.

__SCPM создаёт клиентам каждый раз новый Hosting Space при создание клиенской виртуальной машины.__

User -> new Hosting Space -> new VM

В SolidCP один клиентский Hosting Space должен иметь только одну ВМ, потому что SolidCP не умеют "suspend" определённые объекты, 
только целиком весь Hosting Space.

Пример порядка вызова Rest API
------------
1. Получить токен - /api/login/authentication_token
2. Создание ВМ - /api/solidCP/all-in-one/user/package/vps
3. Подождать пока "provision status" не вернёт статус - "OK" - /api/solidCP/vps/{solidcp_item_id}/provisioning-status
4. Проверить, что ВМ стартовал корректно - вернул состояние - "Running" /api/solidCP/vps/{solidcp_item_id}/state
5. Сервер полностью создан и готов.