<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220927150227 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE franchise ADD sell_drink TINYINT(1) DEFAULT NULL, ADD manage_schedule TINYINT(1) DEFAULT NULL, ADD create_newsletter TINYINT(1) DEFAULT NULL, ADD add_promotion TINYINT(1) DEFAULT NULL, ADD sell_food TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE franchise DROP sell_drink, DROP manage_schedule, DROP create_newsletter, DROP add_promotion, DROP sell_food');
    }
}
