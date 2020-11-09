<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201108115620 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE creator (id INT AUTO_INCREMENT NOT NULL, post_id INT DEFAULT NULL, user VARCHAR(255) NOT NULL, name VARCHAR(255) DEFAULT NULL, surname VARCHAR(255) DEFAULT NULL, participation VARCHAR(255) NOT NULL, is_published TINYINT(1) DEFAULT NULL, create_at DATETIME DEFAULT NULL, update_at DATETIME DEFAULT NULL, INDEX IDX_BC06EA634B89032C (post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(1000) NOT NULL, year DATETIME NOT NULL, doi VARCHAR(1000) DEFAULT NULL, create_at DATETIME NOT NULL, num_of_points INT NOT NULL, conference VARCHAR(1000) NOT NULL, update_at DATETIME DEFAULT NULL, is_published TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, company VARCHAR(250) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, creator VARCHAR(255) NOT NULL, update_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D6494FBF094F (company), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE creator ADD CONSTRAINT FK_BC06EA634B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE creator DROP FOREIGN KEY FK_BC06EA634B89032C');
        $this->addSql('DROP TABLE creator');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE user');
    }
}
