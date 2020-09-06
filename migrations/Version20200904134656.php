<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200904134656 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event ADD token VARCHAR(255) DEFAULT NULL');
        $this->addSql('UPDATE event SET token = SUBSTR(HEX(SHA2(CONCAT(NOW(), RAND(), UUID()), 256)), 1, 20)');
        $this->addSql('ALTER TABLE event MODIFY token VARCHAR(255) NOT NULL');

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

        $this->addSql('ALTER TABLE event DROP token');
    }
}
