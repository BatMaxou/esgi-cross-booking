<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250120215320 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add reset_token to user and make image nullable in raft';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE raft CHANGE image image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD reset_token VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE raft CHANGE image image VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user DROP reset_token');
    }
}
