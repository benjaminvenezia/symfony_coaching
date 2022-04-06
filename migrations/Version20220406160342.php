<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220406160342 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event ADD group_event_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA778C7A4F4 FOREIGN KEY (group_event_id) REFERENCES `group` (id)');
        $this->addSql('CREATE INDEX IDX_3BAE0AA778C7A4F4 ON event (group_event_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA778C7A4F4');
        $this->addSql('DROP INDEX IDX_3BAE0AA778C7A4F4 ON event');
        $this->addSql('ALTER TABLE event DROP group_event_id');
    }
}
