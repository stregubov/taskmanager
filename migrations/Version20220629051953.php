<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220629051953 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE task_priority_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE task_type_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE task_priority (id INT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE task_type (id INT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE task ADD type_id INT NOT NULL');
        $this->addSql('ALTER TABLE task ADD priority_id INT NOT NULL');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25C54C8C93 FOREIGN KEY (type_id) REFERENCES task_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25497B19F9 FOREIGN KEY (priority_id) REFERENCES task_priority (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_527EDB25C54C8C93 ON task (type_id)');
        $this->addSql('CREATE INDEX IDX_527EDB25497B19F9 ON task (priority_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE task DROP CONSTRAINT FK_527EDB25497B19F9');
        $this->addSql('ALTER TABLE task DROP CONSTRAINT FK_527EDB25C54C8C93');
        $this->addSql('DROP SEQUENCE task_priority_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE task_type_id_seq CASCADE');
        $this->addSql('DROP TABLE task_priority');
        $this->addSql('DROP TABLE task_type');
        $this->addSql('DROP INDEX IDX_527EDB25C54C8C93');
        $this->addSql('DROP INDEX IDX_527EDB25497B19F9');
        $this->addSql('ALTER TABLE task DROP type_id');
        $this->addSql('ALTER TABLE task DROP priority_id');
    }
}
