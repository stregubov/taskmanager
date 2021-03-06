<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220706194008 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
//        $this->addSql('ALTER TABLE notification_template ALTER text TYPE VARCHAR(255)');
//        $this->addSql('ALTER TABLE notification_template ALTER text DROP DEFAULT');
        $this->addSql('ALTER TABLE task ADD spent_time_hours VARCHAR(255) NOT NULL DEFAULT 1');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
//        $this->addSql('ALTER TABLE notification_template ALTER text TYPE TEXT');
//        $this->addSql('ALTER TABLE notification_template ALTER text DROP DEFAULT');
        $this->addSql('ALTER TABLE task DROP spent_time_hours');
    }
}
