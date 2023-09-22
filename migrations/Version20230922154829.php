<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230922154829 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE users ADD reset_token VARCHAR(100) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comments CHANGE content content LONGTEXT NOT NULL COLLATE `utf8_unicode_ci`');
        $this->addSql('ALTER TABLE figure_group CHANGE name name VARCHAR(255) NOT NULL COLLATE `utf8_unicode_ci`');
        $this->addSql('ALTER TABLE media CHANGE source source VARCHAR(255) NOT NULL COLLATE `utf8_unicode_ci`');
        $this->addSql('ALTER TABLE tricks CHANGE name name VARCHAR(255) NOT NULL COLLATE `utf8_unicode_ci`, CHANGE description description LONGTEXT NOT NULL COLLATE `utf8_unicode_ci`, CHANGE main_media main_media VARCHAR(255) NOT NULL COLLATE `utf8_unicode_ci`');
        $this->addSql('ALTER TABLE users DROP reset_token, CHANGE username username VARCHAR(255) NOT NULL COLLATE `utf8_unicode_ci`, CHANGE password password VARCHAR(255) NOT NULL COLLATE `utf8_unicode_ci`, CHANGE photo photo VARCHAR(255) NOT NULL COLLATE `utf8_unicode_ci`, CHANGE email email VARCHAR(255) NOT NULL COLLATE `utf8_unicode_ci`');
        $this->addSql('ALTER TABLE videos CHANGE source source LONGTEXT NOT NULL COLLATE `utf8_unicode_ci`');
    }
}
