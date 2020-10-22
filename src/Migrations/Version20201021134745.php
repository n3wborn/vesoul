<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201021134745 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE book ADD height INT NOT NULL, CHANGE author_id author_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE image CHANGE book_id book_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE command CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE address CHANGE type type VARCHAR(45) DEFAULT NULL, CHANGE additional additional VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE birth birth DATE DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE address CHANGE type type VARCHAR(45) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE additional additional VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE book DROP height, CHANGE author_id author_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE command CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE image CHANGE book_id book_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE birth birth DATE DEFAULT \'NULL\'');
    }
}
