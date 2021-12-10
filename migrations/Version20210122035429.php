<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210122035429 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql',
            'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE audit_logs (
                    "id"            UUID NOT NULL,
                    "id_user"       UUID NOT NULL,
                    "date"          TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT NOW(),
                    "ip_inet"       INET NOT NULL,
                    "entity_type"   VARCHAR(255) NOT NULL,
                    "entity_id"     VARCHAR(36) NOT NULL,
                    "task_name"     VARCHAR(255) NOT NULL,
                    "records"       JSONB NOT NULL,
                    PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN audit_logs.id IS \'(DC2Type:audit_log_id)\'');
        $this->addSql('COMMENT ON COLUMN audit_logs.id_user IS \'(DC2Type:audit_log_user_id)\'');
        $this->addSql('COMMENT ON COLUMN audit_logs.date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN audit_logs.entity_type IS \'(DC2Type:audit_log_entity_type)\'');
        $this->addSql('COMMENT ON COLUMN audit_logs.task_name IS \'(DC2Type:audit_log_task_name_type)\'');
        $this->addSql('COMMENT ON COLUMN audit_logs.records IS \'(DC2Type:audit_log_record_type)\'');
        $this->addSql('CREATE INDEX "audit_logs_entity_type_id_idx" ON "audit_logs" ("entity_type", "entity_id")');

        $this->addSql('CREATE TABLE user_users (
                    "id" UUID NOT NULL,
                    "login" VARCHAR(64) NOT NULL,
                    "password" VARCHAR(512) NOT NULL,
                    "date" TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT NOW(),
                    "role" VARCHAR(32) NOT NULL,
                    "status" VARCHAR(32) NOT NULL,
                    PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX user_users_unique_login ON user_users (lower("login"))');
        $this->addSql('COMMENT ON COLUMN user_users.id IS \'(DC2Type:user_user_id)\'');
        $this->addSql('COMMENT ON COLUMN user_users.date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN user_users.role IS \'(DC2Type:user_user_role)\'');
        $this->addSql('COMMENT ON COLUMN user_users.status IS \'(DC2Type:user_user_status)\'');

        $this->addSql('CREATE TABLE "cp_locations" (
            "id"    SERIAL,
            "name"  VARCHAR(255) NOT NULL, --UNIQUE
            CONSTRAINT "pk_cp_locations" PRIMARY KEY ("id")
            )');
        $this->addSql('CREATE UNIQUE INDEX "cp_locations_unique_name" ON "cp_locations" (lower("name"))');

        $this->addSql('CREATE TABLE "cp_packages" (
            "id_package"    UUID NOT NULL, 
            "name"          VARCHAR(128) NOT NULL, --UNIQUE
            "package_type"  VARCHAR(512) NOT NULL,
            CONSTRAINT "pk_cp_packages" PRIMARY KEY ("id_package")                         
            )');
        $this->addSql('CREATE UNIQUE INDEX "cp_packages_unique_name" ON "cp_packages" (lower("name"))');
        $this->addSql('COMMENT ON COLUMN cp_packages.id_package IS \'(DC2Type:cp_package_id)\'');
        $this->addSql('COMMENT ON COLUMN cp_packages.package_type IS \'(DC2Type:cp_package_type)\'');

        $this->addSql('CREATE TABLE "cp_package_virtual_machines" (
            "id_package"    UUID NOT NULL,
            "cores"         INT NOT NULL DEFAULT 1,
            "threads"       INT NOT NULL DEFAULT 1,
            "ram_mb"        INT NOT NULL DEFAULT 1024,
            "space_gb"      INT NOT NULL DEFAULT 32,
            "iops_min"      INT NOT NULL DEFAULT 0,
            "iops_max"      INT NOT NULL DEFAULT 0,
            CONSTRAINT "pk_cp_package_virtual_machines" PRIMARY KEY ("id_package"),
            CONSTRAINT "id_package_cp_package_virtual_machines" FOREIGN KEY ("id_package")
                     REFERENCES "cp_packages"("id_package") ON DELETE CASCADE
                         
            )');
        $this->addSql('CREATE INDEX "cp_package_virtual_machines_id_package_idx" ON "cp_package_virtual_machines" ("id_package")');
        $this->addSql('COMMENT ON COLUMN cp_package_virtual_machines.id_package IS \'(DC2Type:cp_package_id)\'');

        $this->addSql('CREATE TABLE "cp_solidcp_enterprise_dispatchers" (
            "id"                SERIAL NOT NULL,
            "name"              VARCHAR(128) NOT NULL, --UNIQUE
            "url"               VARCHAR(1024) NOT NULL,
            "login"             VARCHAR(64) NOT NULL,
            "password"          VARCHAR(512) NOT NULL,
            "solidcp_login_id"  INT NOT NULL, --get from SOAP call
            "is_default"        BOOLEAN NOT NULL DEFAULT FALSE,
            "enabled"           BOOLEAN NOT NULL DEFAULT TRUE,
            CONSTRAINT "pk_cp_solidcp_enterprise_dispatchers" PRIMARY KEY ("id")                         
            )');
        $this->addSql('CREATE UNIQUE INDEX "cp_solidcp_enterprise_dispatchers_unique_name" ON "cp_solidcp_enterprise_dispatchers" (lower("name"))');
        $this->addSql('CREATE UNIQUE INDEX "cp_solidcp_enterprise_dispatchers_unique_default" ON "cp_solidcp_enterprise_dispatchers" ("id") WHERE "is_default"'); //only one true

        $this->addSql('CREATE TABLE "cp_solidcp_servers" (
            "id"            SERIAL NOT NULL, 
            "id_enterprise_dispatcher" INT NOT NULL,
            "id_location"   INT NOT NULL,
            "name"          VARCHAR(128) NOT NULL, --UNIQUE
            "cores"         INT NOT NULL DEFAULT 1,
            "threads"       INT NOT NULL DEFAULT 1,
            "memory_mb"     BIGINT NOT NULL DEFAULT 1024,
            "enabled"       BOOLEAN NOT NULL DEFAULT TRUE,
            CONSTRAINT "pk_cp_solidcp_servers" PRIMARY KEY ("id"),
            CONSTRAINT "fk_cp_solidcp_servers_id_enterprise_dispatcher" FOREIGN KEY ("id_enterprise_dispatcher")
                REFERENCES "cp_solidcp_enterprise_dispatchers"("id") ON DELETE RESTRICT,
            CONSTRAINT "fk_cp_solidcp_servers_id_location" FOREIGN KEY ("id_location")
                REFERENCES "cp_locations"("id") ON DELETE RESTRICT                            
            )');
        $this->addSql('CREATE UNIQUE INDEX "cp_solidcp_servers_unique_name" ON "cp_solidcp_servers" (lower("name"))');
        $this->addSql('CREATE INDEX "cp_solidcp_servers_id_enterprise_dispatcher_idx" ON "cp_solidcp_servers" ("id_enterprise_dispatcher")');
        $this->addSql('CREATE INDEX "cp_solidcp_servers_id_location_idx" ON "cp_solidcp_servers" ("id_location")');

        $this->addSql('CREATE TABLE "cp_solidcp_hosting_spaces" (
            "id"                        SERIAL NOT NULL, 
            "id_server"                 INT NOT NULL,
            "solidcp_id_hosting_space"  INT NOT NULL,
            "name"                      VARCHAR(128) NOT NULL,
            "max_active_number"         INT NOT NULL,
            "max_reserved_memory_kb"    INT NOT NULL,
            "space_quota_gb"            INT NOT NULL,
            "enabled"                   BOOLEAN NOT NULL DEFAULT TRUE,
            CONSTRAINT "pk_cp_solidcp_hosting_spaces" PRIMARY KEY ("id"),
                CONSTRAINT "fk_cp_solidcp_hosting_spaces_id_server" FOREIGN KEY ("id_server")
                   REFERENCES "cp_solidcp_servers"("id") ON DELETE RESTRICT                         
            )');
        $this->addSql('CREATE UNIQUE INDEX "cp_solidcp_hosting_spaces_unique_name" ON "cp_solidcp_hosting_spaces" (lower("name"))');
        $this->addSql('CREATE INDEX "cp_solidcp_hosting_spaces_id_server_idx" ON "cp_solidcp_hosting_spaces" ("id_server")');

        $this->addSql('CREATE TABLE "cp_solidcp_hosting_space_os_templates" (
            "id"                SERIAL NOT NULL,             
            "id_hosting_space"  INT NOT NULL,
            "path"              VARCHAR(128) NOT NULL,
            "name"              VARCHAR(128) NOT NULL,
            CONSTRAINT "pk_cp_solidcp_hosting_space_os_templates" PRIMARY KEY ("id"),
            CONSTRAINT "unique_ids_cp_solidcp_hosting_space_os_templates" UNIQUE ("path", "id_hosting_space"),
            CONSTRAINT "fk_cp_solidcp_hosting_space_os_templates_id_hosting_space" FOREIGN KEY ("id_hosting_space")
                REFERENCES "cp_solidcp_hosting_spaces"("id") ON DELETE RESTRICT                         
            )');
        $this->addSql('CREATE INDEX "cp_solidcp_hosting_space_os_templates_id_hosting_space_idx" ON "cp_solidcp_hosting_space_os_templates" ("id_hosting_space")');

        $this->addSql('CREATE TABLE "cp_solidcp_hosting_plans" (
            "id"                SERIAL NOT NULL, 
            "id_hosting_space"  INT NOT NULL,
            "solidcp_id_plan"   INT NOT NULL,
            "solidcp_id_server" INT NOT NULL,
            "name"              VARCHAR(128) NOT NULL,
           -- "is_default"        BOOLEAN NOT NULL DEFAULT FALSE,
            CONSTRAINT "pk_cp_solidcp_hosting_plans" PRIMARY KEY ("id"),
            CONSTRAINT "fk_cp_solidcp_hosting_plans_id_hosting_space" FOREIGN KEY ("id_hosting_space")
                REFERENCES "cp_solidcp_hosting_spaces"("id") ON DELETE RESTRICT                         
            )');
        $this->addSql('CREATE UNIQUE INDEX "cp_solidcp_hosting_plans_unique_name" ON "cp_solidcp_hosting_plans" (lower("name"))');
        //$this->addSql('CREATE UNIQUE INDEX "only_one_default_for_id_hosting_space" ON "cp_solidcp_hosting_plans" ("id", "id_hosting_space") WHERE "is_default"');
        $this->addSql('CREATE INDEX "cp_solidcp_hosting_plans_id_hosting_space_idx" ON "cp_solidcp_hosting_plans" ("id_hosting_space")');

        $this->addSql('CREATE TABLE "cp_package_assigned_scp_hosting_plans" (
            --"id"            SERIAL NOT NULL,             
            "id_plan"       INT NOT NULL,
            "id_package"   UUID NOT NULL,
            CONSTRAINT "pk_cp_package_assigned_scp_hosting_plans" PRIMARY KEY ("id_plan", "id_package"),
            CONSTRAINT "fk_cp_package_assigned_scp_hosting_plans_id_plan" FOREIGN KEY ("id_plan")
                REFERENCES "cp_solidcp_hosting_plans"("id") ON DELETE CASCADE  ,
            CONSTRAINT "fk_cp_package_assigned_scp_hosting_plans_id_packages" FOREIGN KEY ("id_package")
                REFERENCES "cp_packages"("id_package") ON DELETE RESTRICT                      
            )');
        $this->addSql('CREATE INDEX "IDX_9334A9A1F5CEE5" ON "cp_package_assigned_scp_hosting_plans" ("id_package")');
        $this->addSql('CREATE INDEX "IDX_9334A9A1567A477F" ON "cp_package_assigned_scp_hosting_plans" ("id_plan")');
        $this->addSql('COMMENT ON COLUMN cp_package_assigned_scp_hosting_plans.id_package IS \'(DC2Type:cp_package_id)\'');




//        $this->addSql('CREATE TABLE cp_panels (
//            id      SERIAL NOT NULL,
//            name    VARCHAR(64) NOT NULL, --UNIQUE
//            CONSTRAINT "pk_cp_panels" PRIMARY KEY ("id")
//
//            )');
//        $this->addSql('CREATE UNIQUE INDEX cp_panels_unique_name ON cp_panels (lower(name))');
//
//        $this->addSql('CREATE TABLE cp_panel_settings (
//            id      SERIAL NOT NULL,
//            id_panel  INT NOT NULL,
//            api_type VARCHAR(64) NOT NULL, --soap/rest
//            url   VARCHAR(1024) NOT NULL,
//            login VARCHAR(64) NOT NULL,
//            password VARCHAR(512) NOT NULL,
//            CONSTRAINT "pk_cp_panel_settings" PRIMARY KEY ("id")
//
//            )');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql',
            'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('
        DO $$ DECLARE
            r RECORD;
        BEGIN
            FOR r IN (SELECT tablename FROM pg_tables WHERE schemaname = current_schema()) LOOP
                    EXECUTE \'DROP TABLE \' || quote_ident(r.tablename) || \' CASCADE\';
                END LOOP;
        END $$'
        );
    }
}