<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220927123143 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE franchise DROP FOREIGN KEY FK_66F6CE2A80409AC0');
        $this->addSql('ALTER TABLE structure DROP FOREIGN KEY FK_6F0137EA62BA1ECE');
        $this->addSql('DROP TABLE global_permissions');
        $this->addSql('DROP TABLE permission');
        $this->addSql('DROP TABLE structure_permissions');
        $this->addSql('DROP INDEX IDX_66F6CE2A80409AC0 ON franchise');
        $this->addSql('ALTER TABLE franchise DROP global_permissions_id');
        $this->addSql('DROP INDEX IDX_6F0137EA62BA1ECE ON structure');
        $this->addSql('ALTER TABLE structure DROP structure_permissions_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE global_permissions (id INT AUTO_INCREMENT NOT NULL, permissions LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:array)\', is_active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE permission (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, is_active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE structure_permissions (id INT AUTO_INCREMENT NOT NULL, permissions LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:array)\', is_active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE franchise ADD global_permissions_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE franchise ADD CONSTRAINT FK_66F6CE2A80409AC0 FOREIGN KEY (global_permissions_id) REFERENCES global_permissions (id)');
        $this->addSql('CREATE INDEX IDX_66F6CE2A80409AC0 ON franchise (global_permissions_id)');
        $this->addSql('ALTER TABLE structure ADD structure_permissions_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE structure ADD CONSTRAINT FK_6F0137EA62BA1ECE FOREIGN KEY (structure_permissions_id) REFERENCES structure_permissions (id)');
        $this->addSql('CREATE INDEX IDX_6F0137EA62BA1ECE ON structure (structure_permissions_id)');
    }
}
