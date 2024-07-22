<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240711123043 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE contact_information (id INT AUTO_INCREMENT NOT NULL, last_name VARCHAR(255) DEFAULT NULL, first_name VARCHAR(255) DEFAULT NULL, company_name VARCHAR(255) DEFAULT NULL, phone_number VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, zip_code VARCHAR(255) DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, siret_nb VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE facturation (id INT AUTO_INCREMENT NOT NULL, value LONGTEXT NOT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE intervention (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE intervention_stock (id INT AUTO_INCREMENT NOT NULL, quantity_used INT NOT NULL, used_at DATETIME NOT NULL, description VARCHAR(255) NOT NULL, intervention_id INT NOT NULL, stock_id INT NOT NULL, INDEX IDX_3E534AE48EAE3863 (intervention_id), INDEX IDX_3E534AE4DCD6110 (stock_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE stock (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, reference_nb VARCHAR(255) NOT NULL, quantity INT NOT NULL, is_active TINYINT(1) NOT NULL, supplier_id INT DEFAULT NULL, INDEX IDX_4B3656602ADD6D8C (supplier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE ticket (id INT AUTO_INCREMENT NOT NULL, date_start DATETIME NOT NULL, date_end DATETIME NOT NULL, status VARCHAR(50) NOT NULL, description LONGTEXT NOT NULL, user_id INT DEFAULT NULL, technicien_id INT DEFAULT NULL, intervention_id INT DEFAULT NULL, INDEX IDX_97A0ADA3A76ED395 (user_id), INDEX IDX_97A0ADA313457256 (technicien_id), UNIQUE INDEX UNIQ_97A0ADA38EAE3863 (intervention_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, roles JSON NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, is_active TINYINT(1) NOT NULL, contact_information_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D6495D0DBFC1 (contact_information_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE intervention_stock ADD CONSTRAINT FK_3E534AE48EAE3863 FOREIGN KEY (intervention_id) REFERENCES intervention (id)');
        $this->addSql('ALTER TABLE intervention_stock ADD CONSTRAINT FK_3E534AE4DCD6110 FOREIGN KEY (stock_id) REFERENCES stock (id)');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B3656602ADD6D8C FOREIGN KEY (supplier_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA313457256 FOREIGN KEY (technicien_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA38EAE3863 FOREIGN KEY (intervention_id) REFERENCES intervention (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6495D0DBFC1 FOREIGN KEY (contact_information_id) REFERENCES contact_information (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE intervention_stock DROP FOREIGN KEY FK_3E534AE48EAE3863');
        $this->addSql('ALTER TABLE intervention_stock DROP FOREIGN KEY FK_3E534AE4DCD6110');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B3656602ADD6D8C');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3A76ED395');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA313457256');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA38EAE3863');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6495D0DBFC1');
        $this->addSql('DROP TABLE contact_information');
        $this->addSql('DROP TABLE facturation');
        $this->addSql('DROP TABLE intervention');
        $this->addSql('DROP TABLE intervention_stock');
        $this->addSql('DROP TABLE stock');
        $this->addSql('DROP TABLE ticket');
        $this->addSql('DROP TABLE user');
    }
}
