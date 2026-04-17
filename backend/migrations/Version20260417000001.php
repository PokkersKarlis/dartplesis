<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260417000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create player table with full profile fields';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE player (
            id INT AUTO_INCREMENT NOT NULL,
            name VARCHAR(100) NOT NULL,
            surname VARCHAR(100) NOT NULL,
            nickname VARCHAR(100) DEFAULT NULL,
            walk_out_song VARCHAR(200) DEFAULT NULL,
            favorite_double VARCHAR(10) DEFAULT NULL,
            dart_weight VARCHAR(20) DEFAULT NULL,
            dart_model VARCHAR(100) DEFAULT NULL,
            grip_style VARCHAR(20) DEFAULT NULL,
            highest_checkout INT DEFAULT NULL,
            career_highlight LONGTEXT DEFAULT NULL,
            pre_game_ritual LONGTEXT DEFAULT NULL,
            darts_idol VARCHAR(100) DEFAULT NULL,
            hobbies VARCHAR(200) DEFAULT NULL,
            life_motto VARCHAR(300) DEFAULT NULL,
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            PRIMARY KEY (id)
        ) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE player');
    }
}
