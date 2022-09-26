<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220926131018 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE franchise (id INT AUTO_INCREMENT NOT NULL, global_permissions_id INT DEFAULT NULL, client_name VARCHAR(120) NOT NULL, client_address VARCHAR(255) NOT NULL, url VARCHAR(255) DEFAULT NULL, logo_url VARCHAR(255) DEFAULT NULL, technical_contact VARCHAR(120) DEFAULT NULL, commercial_contact VARCHAR(255) DEFAULT NULL, short_description VARCHAR(120) NOT NULL, full_description VARCHAR(255) NOT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_66F6CE2A80409AC0 (global_permissions_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE global_permissions (id INT AUTO_INCREMENT NOT NULL, permissions LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', is_active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE permission (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, is_active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE structure (id INT AUTO_INCREMENT NOT NULL, franchise_id_id INT DEFAULT NULL, user_id_id INT DEFAULT NULL, structure_permissions_id INT DEFAULT NULL, structure_name VARCHAR(255) NOT NULL, structure_address VARCHAR(255) NOT NULL, manager_name VARCHAR(120) DEFAULT NULL, logo_url VARCHAR(255) NOT NULL, url VARCHAR(255) DEFAULT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_6F0137EAEA39FCC8 (franchise_id_id), INDEX IDX_6F0137EA9D86650F (user_id_id), INDEX IDX_6F0137EA62BA1ECE (structure_permissions_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE structure_permissions (id INT AUTO_INCREMENT NOT NULL, permissions LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', is_active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE franchise ADD CONSTRAINT FK_66F6CE2A80409AC0 FOREIGN KEY (global_permissions_id) REFERENCES global_permissions (id)');
        $this->addSql('ALTER TABLE structure ADD CONSTRAINT FK_6F0137EAEA39FCC8 FOREIGN KEY (franchise_id_id) REFERENCES franchise (id)');
        $this->addSql('ALTER TABLE structure ADD CONSTRAINT FK_6F0137EA9D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE structure ADD CONSTRAINT FK_6F0137EA62BA1ECE FOREIGN KEY (structure_permissions_id) REFERENCES structure_permissions (id)');
        $this->addSql('ALTER TABLE user ADD franchise_id INT DEFAULT NULL, ADD last_connexion DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649523CAB89 FOREIGN KEY (franchise_id) REFERENCES franchise (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649523CAB89 ON user (franchise_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649523CAB89');
        $this->addSql('ALTER TABLE franchise DROP FOREIGN KEY FK_66F6CE2A80409AC0');
        $this->addSql('ALTER TABLE structure DROP FOREIGN KEY FK_6F0137EAEA39FCC8');
        $this->addSql('ALTER TABLE structure DROP FOREIGN KEY FK_6F0137EA9D86650F');
        $this->addSql('ALTER TABLE structure DROP FOREIGN KEY FK_6F0137EA62BA1ECE');
        $this->addSql('DROP TABLE franchise');
        $this->addSql('DROP TABLE global_permissions');
        $this->addSql('DROP TABLE permission');
        $this->addSql('DROP TABLE structure');
        $this->addSql('DROP TABLE structure_permissions');
        $this->addSql('DROP INDEX IDX_8D93D649523CAB89 ON user');
        $this->addSql('ALTER TABLE user DROP franchise_id, DROP last_connexion');
    }
}
