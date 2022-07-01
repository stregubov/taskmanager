<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220628200810 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_team DROP CONSTRAINT fk_be61ead6d60322ac');
        $this->addSql('DROP SEQUENCE team_role_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE user_team_id_seq CASCADE');
        $this->addSql('CREATE TABLE team_user (team_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(team_id, user_id))');
        $this->addSql('CREATE INDEX IDX_5C722232296CD8AE ON team_user (team_id)');
        $this->addSql('CREATE INDEX IDX_5C722232A76ED395 ON team_user (user_id)');
        $this->addSql('ALTER TABLE team_user ADD CONSTRAINT FK_5C722232296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE team_user ADD CONSTRAINT FK_5C722232A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE team_role');
        $this->addSql('DROP TABLE user_team');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE team_role_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE user_team_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE team_role (id INT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE user_team (id INT NOT NULL, worker_id INT NOT NULL, team_id INT NOT NULL, role_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_be61ead6d60322ac ON user_team (role_id)');
        $this->addSql('CREATE INDEX idx_be61ead6296cd8ae ON user_team (team_id)');
        $this->addSql('CREATE INDEX idx_be61ead66b20ba36 ON user_team (worker_id)');
        $this->addSql('ALTER TABLE user_team ADD CONSTRAINT fk_be61ead66b20ba36 FOREIGN KEY (worker_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_team ADD CONSTRAINT fk_be61ead6296cd8ae FOREIGN KEY (team_id) REFERENCES team (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_team ADD CONSTRAINT fk_be61ead6d60322ac FOREIGN KEY (role_id) REFERENCES team_role (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE team_user');
    }
}
