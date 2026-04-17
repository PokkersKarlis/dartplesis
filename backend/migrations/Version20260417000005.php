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

    public function up(Schema $schema): void
    {
        // ── club_request ──────────────────────────────────────────────────────
        // list() filters by status ORDER BY created_at DESC
        // countByIpSince() filters ip_address + created_at (rate-limit check)
        $this->addSql(<<<'SQL'
            ALTER TABLE club_request
                ADD INDEX idx_creq_status_created (status, created_at DESC),
                ADD INDEX idx_creq_ip_created     (ip_address, created_at)
        SQL);

        // ── supporter ─────────────────────────────────────────────────────────
        // publicList() ORDER BY sort_order ASC, created_at ASC
        $this->addSql(<<<'SQL'
            ALTER TABLE supporter
                ADD INDEX idx_supporter_sort (sort_order, created_at)
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE club_request DROP INDEX idx_creq_status_created, DROP INDEX idx_creq_ip_created');

        $this->addSql('ALTER TABLE supporter    DROP INDEX idx_supporter_sort');
    }
}
