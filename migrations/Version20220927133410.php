<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220927133410 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE franchise_permissions (id INT AUTO_INCREMENT NOT NULL, sell_drink TINYINT(1) DEFAULT NULL, manage_schedule TINYINT(1) DEFAULT NULL, create_newsletter TINYINT(1) DEFAULT NULL, add_promotion TINYINT(1) DEFAULT NULL, create_event TINYINT(1) DEFAULT NULL, sell_food TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE franchise ADD franchise_permissions_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE franchise ADD CONSTRAINT FK_66F6CE2ADBC853B5 FOREIGN KEY (franchise_permissions_id) REFERENCES franchise_permissions (id)');
        $this->addSql('CREATE INDEX IDX_66F6CE2ADBC853B5 ON franchise (franchise_permissions_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE franchise DROP FOREIGN KEY FK_66F6CE2ADBC853B5');
        $this->addSql('DROP TABLE franchise_permissions');
        $this->addSql('DROP INDEX IDX_66F6CE2ADBC853B5 ON franchise');
        $this->addSql('ALTER TABLE franchise DROP franchise_permissions_id');
    }
}
