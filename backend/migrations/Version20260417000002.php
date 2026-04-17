<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260417000002 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add dob/photo/isJunior/clubRoles to player; create club_request table';
    }

    public function up(Schema $schema): void
    {
        // Add new columns to player (club_roles as NULL — Doctrine handles null→[] in PHP)
        $this->addSql('ALTER TABLE player
            ADD COLUMN date_of_birth DATE DEFAULT NULL,
            ADD COLUMN photo VARCHAR(255) DEFAULT NULL,
            ADD COLUMN is_junior TINYINT(1) NOT NULL DEFAULT 0,
            ADD COLUMN club_roles LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\'
        ');

        // Initialise existing rows with empty JSON array
        $this->addSql("UPDATE player SET club_roles = '[]' WHERE club_roles IS NULL");

        // Create club_request table (new table, no existing rows — NOT NULL without DEFAULT is safe)
        $this->addSql('CREATE TABLE club_request (
            id INT AUTO_INCREMENT NOT NULL,
            name VARCHAR(100) NOT NULL,
            surname VARCHAR(100) NOT NULL,
            email VARCHAR(180) NOT NULL,
            phone VARCHAR(30) NOT NULL,
            nickname VARCHAR(100) DEFAULT NULL,
            date_of_birth DATE DEFAULT NULL,
            photo VARCHAR(255) DEFAULT NULL,
            is_junior TINYINT(1) NOT NULL DEFAULT 0,
            club_roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\',
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
            status VARCHAR(20) NOT NULL DEFAULT \'pending\',
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            reviewed_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            approved_player_id INT DEFAULT NULL,
            PRIMARY KEY (id)
        ) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE player
            DROP COLUMN date_of_birth,
            DROP COLUMN photo,
            DROP COLUMN is_junior,
            DROP COLUMN club_roles
        ');
        $this->addSql('DROP TABLE club_request');
    }
}
