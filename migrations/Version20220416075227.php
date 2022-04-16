<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220416075227 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA38C748770');
        $this->addSql('DROP INDEX IDX_97A0ADA38C748770 ON ticket');
        $this->addSql('ALTER TABLE ticket CHANGE link_token_id group_ticket_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3BB32B7E5 FOREIGN KEY (group_ticket_id_id) REFERENCES `group` (id)');
        $this->addSql('CREATE INDEX IDX_97A0ADA3BB32B7E5 ON ticket (group_ticket_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3BB32B7E5');
        $this->addSql('DROP INDEX IDX_97A0ADA3BB32B7E5 ON ticket');
        $this->addSql('ALTER TABLE ticket CHANGE group_ticket_id_id link_token_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA38C748770 FOREIGN KEY (link_token_id) REFERENCES `group` (id)');
        $this->addSql('CREATE INDEX IDX_97A0ADA38C748770 ON ticket (link_token_id)');
    }
}
