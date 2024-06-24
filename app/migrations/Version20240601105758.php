<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240601105758 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        /*$this->addSql('CREATE TABLE attachments (id INT AUTO_INCREMENT NOT NULL, report_id INT NOT NULL, filename VARCHAR(191) NOT NULL, UNIQUE INDEX UNIQ_47C4FAD64BD2A4C0 (report_id), UNIQUE INDEX uq_attachment_filename (filename), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE attachments ADD CONSTRAINT FK_47C4FAD64BD2A4C0 FOREIGN KEY (report_id) REFERENCES reports (id)');*/
        $this->addSql('CREATE TABLE attachments (id INT AUTO_INCREMENT NOT NULL, filename VARCHAR(191) NOT NULL, UNIQUE INDEX uq_attachment_filename (filename), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reports ADD attachment_id INT');
        $this->addSql('ALTER TABLE reports ADD CONSTRAINT FK_47C4FAD64BD2A4C0 FOREIGN KEY (attachment_id) REFERENCES attachments (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE attachments DROP FOREIGN KEY FK_47C4FAD64BD2A4C0');
        $this->addSql('ALTER TABLE reports DROP attachment_id');
        $this->addSql('DROP TABLE attachments');
    }
}
