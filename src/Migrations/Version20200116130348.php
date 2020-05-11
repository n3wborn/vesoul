<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200116130348 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE address (id INT AUTO_INCREMENT NOT NULL, number VARCHAR(10) NOT NULL, type VARCHAR(45) DEFAULT NULL, street VARCHAR(150) NOT NULL, city VARCHAR(150) NOT NULL, cp VARCHAR(10) NOT NULL, country VARCHAR(100) NOT NULL, additional VARCHAR(255) DEFAULT NULL, title VARCHAR(45) NOT NULL, firstname VARCHAR(100) NOT NULL, lastname VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE admin (id INT AUTO_INCREMENT NOT NULL, company VARCHAR(100) NOT NULL, tel VARCHAR(30) NOT NULL, libelle VARCHAR(255) NOT NULL, city VARCHAR(150) NOT NULL, cp VARCHAR(10) NOT NULL, country VARCHAR(100) NOT NULL, email VARCHAR(150) NOT NULL, username VARCHAR(100) NOT NULL, password VARCHAR(150) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE author (id INT AUTO_INCREMENT NOT NULL, firstname VARCHAR(150) NOT NULL, lastname VARCHAR(150) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, description LONGTEXT NOT NULL, price DOUBLE PRECISION NOT NULL, isbn VARCHAR(100) NOT NULL, stock INT NOT NULL, title VARCHAR(150) NOT NULL, year INT NOT NULL, length INT NOT NULL, width INT NOT NULL, new TINYINT(1) NOT NULL, INDEX IDX_CBE5A331F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book_genra (book_id INT NOT NULL, genra_id INT NOT NULL, INDEX IDX_8AFFE29816A2B381 (book_id), INDEX IDX_8AFFE298CDF44448 (genra_id), PRIMARY KEY(book_id, genra_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cart (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, price INT NOT NULL, quantity INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE command (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, facturation_id INT NOT NULL, livraison_id INT NOT NULL, date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', number VARCHAR(150) NOT NULL, quantity INT NOT NULL, totalcost DOUBLE PRECISION NOT NULL, state VARCHAR(150) NOT NULL, INDEX IDX_8ECAEAD4A76ED395 (user_id), INDEX IDX_8ECAEAD4DBC5F284 (facturation_id), INDEX IDX_8ECAEAD48E54FB25 (livraison_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE command_book (command_id INT NOT NULL, book_id INT NOT NULL, INDEX IDX_6A4F0DBC33E1689A (command_id), INDEX IDX_6A4F0DBC16A2B381 (book_id), PRIMARY KEY(command_id, book_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE genra (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, book_id INT DEFAULT NULL, url VARCHAR(255) NOT NULL, INDEX IDX_C53D045F16A2B381 (book_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, gender TINYINT(1) NOT NULL, firstname VARCHAR(100) NOT NULL, lastname VARCHAR(100) NOT NULL, password VARCHAR(150) NOT NULL, username VARCHAR(100) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', tel VARCHAR(30) NOT NULL, newsletter TINYINT(1) DEFAULT \'1\' NOT NULL, birth DATE DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_address (user_id INT NOT NULL, address_id INT NOT NULL, INDEX IDX_5543718BA76ED395 (user_id), INDEX IDX_5543718BF5B7AF75 (address_id), PRIMARY KEY(user_id, address_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A331F675F31B FOREIGN KEY (author_id) REFERENCES author (id)');
        $this->addSql('ALTER TABLE book_genra ADD CONSTRAINT FK_8AFFE29816A2B381 FOREIGN KEY (book_id) REFERENCES book (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE book_genra ADD CONSTRAINT FK_8AFFE298CDF44448 FOREIGN KEY (genra_id) REFERENCES genra (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE command ADD CONSTRAINT FK_8ECAEAD4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE command ADD CONSTRAINT FK_8ECAEAD4DBC5F284 FOREIGN KEY (facturation_id) REFERENCES address (id)');
        $this->addSql('ALTER TABLE command ADD CONSTRAINT FK_8ECAEAD48E54FB25 FOREIGN KEY (livraison_id) REFERENCES address (id)');
        $this->addSql('ALTER TABLE command_book ADD CONSTRAINT FK_6A4F0DBC33E1689A FOREIGN KEY (command_id) REFERENCES command (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE command_book ADD CONSTRAINT FK_6A4F0DBC16A2B381 FOREIGN KEY (book_id) REFERENCES book (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045F16A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE user_address ADD CONSTRAINT FK_5543718BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_address ADD CONSTRAINT FK_5543718BF5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE command DROP FOREIGN KEY FK_8ECAEAD4DBC5F284');
        $this->addSql('ALTER TABLE command DROP FOREIGN KEY FK_8ECAEAD48E54FB25');
        $this->addSql('ALTER TABLE user_address DROP FOREIGN KEY FK_5543718BF5B7AF75');
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A331F675F31B');
        $this->addSql('ALTER TABLE book_genra DROP FOREIGN KEY FK_8AFFE29816A2B381');
        $this->addSql('ALTER TABLE command_book DROP FOREIGN KEY FK_6A4F0DBC16A2B381');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045F16A2B381');
        $this->addSql('ALTER TABLE command_book DROP FOREIGN KEY FK_6A4F0DBC33E1689A');
        $this->addSql('ALTER TABLE book_genra DROP FOREIGN KEY FK_8AFFE298CDF44448');
        $this->addSql('ALTER TABLE command DROP FOREIGN KEY FK_8ECAEAD4A76ED395');
        $this->addSql('ALTER TABLE user_address DROP FOREIGN KEY FK_5543718BA76ED395');
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE admin');
        $this->addSql('DROP TABLE author');
        $this->addSql('DROP TABLE book');
        $this->addSql('DROP TABLE book_genra');
        $this->addSql('DROP TABLE cart');
        $this->addSql('DROP TABLE command');
        $this->addSql('DROP TABLE command_book');
        $this->addSql('DROP TABLE genra');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_address');
    }
}
