<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260417000004 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create blog_post table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE blog_post (
            id INT AUTO_INCREMENT NOT NULL,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL,
            content LONGTEXT NOT NULL,
            excerpt LONGTEXT DEFAULT NULL,
            cover_image VARCHAR(255) DEFAULT NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'draft',
            published_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
            meta_title VARCHAR(255) DEFAULT NULL,
            meta_description VARCHAR(300) DEFAULT NULL,
            created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            UNIQUE INDEX UNIQ_BA5AE01D989D9B62 (slug),
            INDEX IDX_STATUS (status),
            INDEX IDX_PUBLISHED_AT (published_at),
            PRIMARY KEY (id)
        ) DEFAULT CHARACTER SET utf8mb4");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE blog_post');
    }
}
