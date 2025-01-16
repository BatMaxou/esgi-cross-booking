<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250116103933 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial Migration';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE company (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE crossing (id INT AUTO_INCREMENT NOT NULL, route_id INT NOT NULL, date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_1145D3AC34ECB4E6 (route_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE crossing_raft (crossing_id INT NOT NULL, raft_id INT NOT NULL, INDEX IDX_343317D81A8042E6 (crossing_id), INDEX IDX_343317D84E1C6B87 (raft_id), PRIMARY KEY(crossing_id, raft_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE port (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE raft (id INT AUTO_INCREMENT NOT NULL, company_id INT NOT NULL, name VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL, places INT NOT NULL, INDEX IDX_CDA86F18979B1AD6 (company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, crossing_id INT NOT NULL, discr VARCHAR(255) NOT NULL, INDEX IDX_42C849551A8042E6 (crossing_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE review (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, crossing_id INT NOT NULL, content LONGTEXT NOT NULL, INDEX IDX_794381C6F675F31B (author_id), INDEX IDX_794381C61A8042E6 (crossing_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE route (id INT AUTO_INCREMENT NOT NULL, from_port_id INT NOT NULL, to_port_id INT NOT NULL, INDEX IDX_2C42079F0B7C933 (from_port_id), INDEX IDX_2C42079F8711769 (to_port_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE simple_reservation (id INT NOT NULL, passenger_id INT NOT NULL, INDEX IDX_B2A50A4B4502E565 (passenger_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE site_message (id INT AUTO_INCREMENT NOT NULL, place VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team (id INT AUTO_INCREMENT NOT NULL, creator_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_C4E0A61F61220EA6 (creator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team_user (team_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_5C722232296CD8AE (team_id), INDEX IDX_5C722232A76ED395 (user_id), PRIMARY KEY(team_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team_reservation (id INT NOT NULL, team_id INT NOT NULL, INDEX IDX_A9F8A618296CD8AE (team_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE crossing ADD CONSTRAINT FK_1145D3AC34ECB4E6 FOREIGN KEY (route_id) REFERENCES route (id)');
        $this->addSql('ALTER TABLE crossing_raft ADD CONSTRAINT FK_343317D81A8042E6 FOREIGN KEY (crossing_id) REFERENCES crossing (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE crossing_raft ADD CONSTRAINT FK_343317D84E1C6B87 FOREIGN KEY (raft_id) REFERENCES raft (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE raft ADD CONSTRAINT FK_CDA86F18979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849551A8042E6 FOREIGN KEY (crossing_id) REFERENCES crossing (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C61A8042E6 FOREIGN KEY (crossing_id) REFERENCES crossing (id)');
        $this->addSql('ALTER TABLE route ADD CONSTRAINT FK_2C42079F0B7C933 FOREIGN KEY (from_port_id) REFERENCES port (id)');
        $this->addSql('ALTER TABLE route ADD CONSTRAINT FK_2C42079F8711769 FOREIGN KEY (to_port_id) REFERENCES port (id)');
        $this->addSql('ALTER TABLE simple_reservation ADD CONSTRAINT FK_B2A50A4B4502E565 FOREIGN KEY (passenger_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE simple_reservation ADD CONSTRAINT FK_B2A50A4BBF396750 FOREIGN KEY (id) REFERENCES reservation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE team ADD CONSTRAINT FK_C4E0A61F61220EA6 FOREIGN KEY (creator_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE team_user ADD CONSTRAINT FK_5C722232296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE team_user ADD CONSTRAINT FK_5C722232A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE team_reservation ADD CONSTRAINT FK_A9F8A618296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE team_reservation ADD CONSTRAINT FK_A9F8A618BF396750 FOREIGN KEY (id) REFERENCES reservation (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE crossing DROP FOREIGN KEY FK_1145D3AC34ECB4E6');
        $this->addSql('ALTER TABLE crossing_raft DROP FOREIGN KEY FK_343317D81A8042E6');
        $this->addSql('ALTER TABLE crossing_raft DROP FOREIGN KEY FK_343317D84E1C6B87');
        $this->addSql('ALTER TABLE raft DROP FOREIGN KEY FK_CDA86F18979B1AD6');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849551A8042E6');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6F675F31B');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C61A8042E6');
        $this->addSql('ALTER TABLE route DROP FOREIGN KEY FK_2C42079F0B7C933');
        $this->addSql('ALTER TABLE route DROP FOREIGN KEY FK_2C42079F8711769');
        $this->addSql('ALTER TABLE simple_reservation DROP FOREIGN KEY FK_B2A50A4B4502E565');
        $this->addSql('ALTER TABLE simple_reservation DROP FOREIGN KEY FK_B2A50A4BBF396750');
        $this->addSql('ALTER TABLE team DROP FOREIGN KEY FK_C4E0A61F61220EA6');
        $this->addSql('ALTER TABLE team_user DROP FOREIGN KEY FK_5C722232296CD8AE');
        $this->addSql('ALTER TABLE team_user DROP FOREIGN KEY FK_5C722232A76ED395');
        $this->addSql('ALTER TABLE team_reservation DROP FOREIGN KEY FK_A9F8A618296CD8AE');
        $this->addSql('ALTER TABLE team_reservation DROP FOREIGN KEY FK_A9F8A618BF396750');
        $this->addSql('DROP TABLE company');
        $this->addSql('DROP TABLE crossing');
        $this->addSql('DROP TABLE crossing_raft');
        $this->addSql('DROP TABLE port');
        $this->addSql('DROP TABLE raft');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE route');
        $this->addSql('DROP TABLE simple_reservation');
        $this->addSql('DROP TABLE site_message');
        $this->addSql('DROP TABLE team');
        $this->addSql('DROP TABLE team_user');
        $this->addSql('DROP TABLE team_reservation');
        $this->addSql('DROP TABLE user');
    }
}
