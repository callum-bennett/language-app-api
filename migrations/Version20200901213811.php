<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200901213811 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, image_url VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE word (id INT AUTO_INCREMENT NOT NULL, opposite_gender_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, translation VARCHAR(255) NOT NULL, gender VARCHAR(1) DEFAULT NULL, UNIQUE INDEX UNIQ_C3F175116B72F733 (opposite_gender_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE word_category (word_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_22F2C810E357438D (word_id), INDEX IDX_22F2C81012469DE2 (category_id), PRIMARY KEY(word_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE word_attempt (id INT AUTO_INCREMENT NOT NULL, word_id INT NOT NULL, correct INT NOT NULL, wrong INT NOT NULL, last_attempt INT NOT NULL, UNIQUE INDEX UNIQ_129E7595E357438D (word_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE word ADD CONSTRAINT FK_C3F175116B72F733 FOREIGN KEY (opposite_gender_id) REFERENCES word (id)');
        $this->addSql('ALTER TABLE word_category ADD CONSTRAINT FK_22F2C810E357438D FOREIGN KEY (word_id) REFERENCES word (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE word_category ADD CONSTRAINT FK_22F2C81012469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE word_attempt ADD CONSTRAINT FK_129E7595E357438D FOREIGN KEY (word_id) REFERENCES word (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE word_category DROP FOREIGN KEY FK_22F2C81012469DE2');
        $this->addSql('ALTER TABLE word DROP FOREIGN KEY FK_C3F175116B72F733');
        $this->addSql('ALTER TABLE word_category DROP FOREIGN KEY FK_22F2C810E357438D');
        $this->addSql('ALTER TABLE word_attempt DROP FOREIGN KEY FK_129E7595E357438D');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE word');
        $this->addSql('DROP TABLE word_category');
        $this->addSql('DROP TABLE word_attempt');
    }
}
