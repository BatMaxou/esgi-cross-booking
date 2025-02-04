<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250204211719 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add date to review';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE review ADD date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE review DROP date');
    }
}
