<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260417000003 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add ip_address column to club_request for rate limiting';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE club_request ADD COLUMN ip_address VARCHAR(45) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE club_request DROP COLUMN ip_address');
    }
}
