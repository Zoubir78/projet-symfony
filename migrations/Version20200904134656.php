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
        $this->addSql('DROP TABLE participe');
        $this->addSql('ALTER TABLE event ADD token VARCHAR(255) DEFAULT NULL');
        $this->addSql('UPDATE event SET token = SUBSTR(HEX(SHA2(CONCAT(NOW(), RAND(), UUID()), 256)), 1, 20)');
        $this->addSql('ALTER TABLE event MODIFY token VARCHAR(255) NOT NULL');

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE participe (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, events_id INT DEFAULT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, date DATETIME NOT NULL, INDEX IDX_9FFA8D4F675F31B (author_id), UNIQUE INDEX UNIQ_9FFA8D49D6A1065 (events_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE participe ADD CONSTRAINT FK_9FFA8D49D6A1065 FOREIGN KEY (events_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE participe ADD CONSTRAINT FK_9FFA8D4F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE event DROP token');
    }
}
