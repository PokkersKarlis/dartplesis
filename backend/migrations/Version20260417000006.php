<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260417000006 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create supporter table with sort index';
    }

    public function isTransactional(): bool
    {
        return false;
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE IF NOT EXISTS supporter (
            id INT AUTO_INCREMENT NOT NULL,
            name VARCHAR(200) NOT NULL,
            logo VARCHAR(255) DEFAULT NULL,
            url VARCHAR(255) DEFAULT NULL,
            sort_order INT NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            PRIMARY KEY (id)
        ) DEFAULT CHARACTER SET utf8mb4');

        $this->addSql('ALTER TABLE supporter ADD INDEX idx_supporter_sort (sort_order, created_at)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS supporter');
    }
}
