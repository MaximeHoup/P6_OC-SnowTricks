<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220329130351 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comments ADD tricks_id INT NOT NULL');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A3B153154 FOREIGN KEY (tricks_id) REFERENCES tricks (id)');
        $this->addSql('CREATE INDEX IDX_5F9E962A3B153154 ON comments (tricks_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962A3B153154');
        $this->addSql('DROP INDEX IDX_5F9E962A3B153154 ON comments');
        $this->addSql('ALTER TABLE comments DROP tricks_id, CHANGE content content LONGTEXT NOT NULL COLLATE `utf8_unicode_ci`');
        $this->addSql('ALTER TABLE media CHANGE source source VARCHAR(255) NOT NULL COLLATE `utf8_unicode_ci`');
        $this->addSql('ALTER TABLE tricks CHANGE name name VARCHAR(255) NOT NULL COLLATE `utf8_unicode_ci`, CHANGE description description LONGTEXT NOT NULL COLLATE `utf8_unicode_ci`, CHANGE figure_group figure_group VARCHAR(255) NOT NULL COLLATE `utf8_unicode_ci`, CHANGE main_media main_media VARCHAR(255) NOT NULL COLLATE `utf8_unicode_ci`');
        $this->addSql('ALTER TABLE users CHANGE username username VARCHAR(255) NOT NULL COLLATE `utf8_unicode_ci`, CHANGE password password VARCHAR(255) NOT NULL COLLATE `utf8_unicode_ci`, CHANGE photo photo VARCHAR(255) NOT NULL COLLATE `utf8_unicode_ci`, CHANGE email email VARCHAR(255) NOT NULL COLLATE `utf8_unicode_ci`');
    }
}
