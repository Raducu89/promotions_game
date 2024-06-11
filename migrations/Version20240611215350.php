<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240611215350 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE partners (id INT AUTO_INCREMENT NOT NULL, csv_partner_id INT NOT NULL, partner_code VARCHAR(100) NOT NULL, name VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, language VARCHAR(10) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE prize_distribution (id INT AUTO_INCREMENT NOT NULL, prize_id INT NOT NULL, date DATE NOT NULL, distributed TINYINT(1) NOT NULL, INDEX IDX_628BD3BCBBE43214 (prize_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE prizes (id INT AUTO_INCREMENT NOT NULL, partner_id INT NOT NULL, prize_id INT NOT NULL, prize_code VARCHAR(100) NOT NULL, name VARCHAR(255) NOT NULL, language VARCHAR(10) NOT NULL, is_available TINYINT(1) NOT NULL, INDEX IDX_F73CF5A69393F8FE (partner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_prizes (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, prize_distribution_id INT NOT NULL, date_played DATE NOT NULL, INDEX IDX_926EEE9BA76ED395 (user_id), INDEX IDX_926EEE9B7A7A0E52 (prize_distribution_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(100) NOT NULL, password VARCHAR(255) NOT NULL, language VARCHAR(10) NOT NULL, UNIQUE INDEX UNIQ_1483A5E9F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE prize_distribution ADD CONSTRAINT FK_628BD3BCBBE43214 FOREIGN KEY (prize_id) REFERENCES prizes (id)');
        $this->addSql('ALTER TABLE prizes ADD CONSTRAINT FK_F73CF5A69393F8FE FOREIGN KEY (partner_id) REFERENCES partners (id)');
        $this->addSql('ALTER TABLE user_prizes ADD CONSTRAINT FK_926EEE9BA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE user_prizes ADD CONSTRAINT FK_926EEE9B7A7A0E52 FOREIGN KEY (prize_distribution_id) REFERENCES prize_distribution (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE prize_distribution DROP FOREIGN KEY FK_628BD3BCBBE43214');
        $this->addSql('ALTER TABLE prizes DROP FOREIGN KEY FK_F73CF5A69393F8FE');
        $this->addSql('ALTER TABLE user_prizes DROP FOREIGN KEY FK_926EEE9BA76ED395');
        $this->addSql('ALTER TABLE user_prizes DROP FOREIGN KEY FK_926EEE9B7A7A0E52');
        $this->addSql('DROP TABLE partners');
        $this->addSql('DROP TABLE prize_distribution');
        $this->addSql('DROP TABLE prizes');
        $this->addSql('DROP TABLE user_prizes');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
