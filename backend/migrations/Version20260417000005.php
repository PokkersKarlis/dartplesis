<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Performance indexes for club_request and supporter tables.
 * (blog_post and player indexes were applied directly to the DB already.)
 */
final class Version20260417000005 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add performance indexes on club_request and supporter tables';
    }

    public function isTransactional(): bool
    {
        return false;
    }

    public function up(Schema $schema): void
    {
        // ── club_request ──────────────────────────────────────────────────────
        $this->addSql('ALTER TABLE club_request DROP INDEX IF EXISTS idx_creq_status_created, DROP INDEX IF EXISTS idx_creq_ip_created');
        $this->addSql('ALTER TABLE club_request ADD INDEX idx_creq_status_created (status, created_at), ADD INDEX idx_creq_ip_created (ip_address, created_at)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE club_request DROP INDEX idx_creq_status_created, DROP INDEX idx_creq_ip_created');
    }
}
