<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220628194658 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE project_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE status_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE team_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE team_role_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE user_team_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE project (id INT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE status (id INT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE team (id INT NOT NULL, project_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C4E0A61F166D1F9C ON team (project_id)');
        $this->addSql('CREATE TABLE team_role (id INT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE user_team (id INT NOT NULL, worker_id INT NOT NULL, team_id INT NOT NULL, role_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BE61EAD66B20BA36 ON user_team (worker_id)');
        $this->addSql('CREATE INDEX IDX_BE61EAD6296CD8AE ON user_team (team_id)');
        $this->addSql('CREATE INDEX IDX_BE61EAD6D60322AC ON user_team (role_id)');
        $this->addSql('ALTER TABLE team ADD CONSTRAINT FK_C4E0A61F166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_team ADD CONSTRAINT FK_BE61EAD66B20BA36 FOREIGN KEY (worker_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_team ADD CONSTRAINT FK_BE61EAD6296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_team ADD CONSTRAINT FK_BE61EAD6D60322AC FOREIGN KEY (role_id) REFERENCES team_role (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE team DROP CONSTRAINT FK_C4E0A61F166D1F9C');
        $this->addSql('ALTER TABLE user_team DROP CONSTRAINT FK_BE61EAD6296CD8AE');
        $this->addSql('ALTER TABLE user_team DROP CONSTRAINT FK_BE61EAD6D60322AC');
        $this->addSql('DROP SEQUENCE project_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE status_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE team_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE team_role_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE user_team_id_seq CASCADE');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE status');
        $this->addSql('DROP TABLE team');
        $this->addSql('DROP TABLE team_role');
        $this->addSql('DROP TABLE user_team');
    }
}
