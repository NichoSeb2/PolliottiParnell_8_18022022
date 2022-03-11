<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220310130549 extends AbstractMigration {
    public function getDescription(): string {
        return 'updated entity to 5.3 standard';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE task ADD updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE user ADD roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL, CHANGE username username VARCHAR(180) NOT NULL, CHANGE password password VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE task DROP updated_at');
        $this->addSql('ALTER TABLE user DROP roles, DROP created_at, DROP updated_at, CHANGE username username VARCHAR(25) NOT NULL, CHANGE password password VARCHAR(64) NOT NULL');
    }
}
