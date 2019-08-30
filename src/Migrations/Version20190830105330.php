<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190830105330 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE currencies (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) DEFAULT NULL, symbol VARCHAR(4) DEFAULT NULL, ratio DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE prodcategories (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, is_active TINYINT(1) DEFAULT \'1\' NOT NULL, description TEXT DEFAULT NULL, UNIQUE INDEX UNIQ_82CE3B905E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, price NUMERIC(10, 2) NOT NULL, trade_price NUMERIC(10, 2) NOT NULL, packs INT NOT NULL, in_pack INT NOT NULL, out_pack INT NOT NULL, article VARCHAR(100) NOT NULL, is_active TINYINT(1) DEFAULT \'1\' NOT NULL, description TEXT DEFAULT NULL, UNIQUE INDEX UNIQ_B3BA5A5A23A0E66 (article), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_prodcategory (product_id INT NOT NULL, prodcategory_id INT NOT NULL, INDEX IDX_3678C73F4584665A (product_id), INDEX IDX_3678C73FA198DAB1 (prodcategory_id), PRIMARY KEY(product_id, prodcategory_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, firstname VARCHAR(50) DEFAULT NULL, surname VARCHAR(50) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, phone VARCHAR(50) DEFAULT NULL, postcode VARCHAR(50) DEFAULT NULL, mail_addr VARCHAR(100) DEFAULT NULL, confirmed TINYINT(1) DEFAULT \'0\' NOT NULL, UNIQUE INDEX UNIQ_1483A5E9F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_types (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, rights INT NOT NULL, description VARCHAR(255) DEFAULT NULL, UNIQUE INDEX name_indx (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product_prodcategory ADD CONSTRAINT FK_3678C73F4584665A FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_prodcategory ADD CONSTRAINT FK_3678C73FA198DAB1 FOREIGN KEY (prodcategory_id) REFERENCES prodcategories (id) ON DELETE CASCADE');

        $this->addSql("INSERT INTO users (username, roles, password, confirmed) VALUES ('root', '[\"ROOT\"]', 'rootadmin', 1)");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product_prodcategory DROP FOREIGN KEY FK_3678C73FA198DAB1');
        $this->addSql('ALTER TABLE product_prodcategory DROP FOREIGN KEY FK_3678C73F4584665A');
        $this->addSql('DROP TABLE currencies');
        $this->addSql('DROP TABLE prodcategories');
        $this->addSql('DROP TABLE products');
        $this->addSql('DROP TABLE product_prodcategory');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE user_types');
    }
}
