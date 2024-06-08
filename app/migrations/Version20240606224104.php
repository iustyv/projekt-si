<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240606224104 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reports ADD assigned_to_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reports ADD CONSTRAINT FK_F11FA745F4BD7827 FOREIGN KEY (assigned_to_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_F11FA745F4BD7827 ON reports (assigned_to_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reports DROP FOREIGN KEY FK_F11FA745F4BD7827');
        $this->addSql('DROP INDEX IDX_F11FA745F4BD7827 ON reports');
        $this->addSql('ALTER TABLE reports DROP assigned_to_id');
    }
}
