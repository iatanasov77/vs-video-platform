<?php

declare(strict_types=1);

namespace App\DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230625165107 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE VVP_VideoCategories (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, taxon_id INT NOT NULL, INDEX IDX_2A33723D727ACA70 (parent_id), UNIQUE INDEX UNIQ_2A33723DDE13F470 (taxon_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VVP_Videos (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, slug VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, public TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_49B70CBA989D9B62 (slug), INDEX IDX_49B70CBAA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VVP_Videos_Categories (video_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_AB2A07DB29C1004E (video_id), INDEX IDX_AB2A07DB12469DE2 (category_id), PRIMARY KEY(video_id, category_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VVP_Videos_Files (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, type VARCHAR(255) DEFAULT NULL, path VARCHAR(255) NOT NULL, original_name VARCHAR(255) DEFAULT \'\' NOT NULL COMMENT \'The Original Name of the File.\', UNIQUE INDEX UNIQ_CC839EBF7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE VVP_VideoCategories ADD CONSTRAINT FK_2A33723D727ACA70 FOREIGN KEY (parent_id) REFERENCES VVP_VideoCategories (id)');
        $this->addSql('ALTER TABLE VVP_VideoCategories ADD CONSTRAINT FK_2A33723DDE13F470 FOREIGN KEY (taxon_id) REFERENCES VSAPP_Taxons (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE VVP_Videos ADD CONSTRAINT FK_49B70CBAA76ED395 FOREIGN KEY (user_id) REFERENCES VSUM_Users (id)');
        $this->addSql('ALTER TABLE VVP_Videos_Categories ADD CONSTRAINT FK_AB2A07DB29C1004E FOREIGN KEY (video_id) REFERENCES VVP_Videos (id)');
        $this->addSql('ALTER TABLE VVP_Videos_Categories ADD CONSTRAINT FK_AB2A07DB12469DE2 FOREIGN KEY (category_id) REFERENCES VVP_VideoCategories (id)');
        $this->addSql('ALTER TABLE VVP_Videos_Files ADD CONSTRAINT FK_CC839EBF7E3C61F9 FOREIGN KEY (owner_id) REFERENCES VVP_Videos (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE VSAPP_Settings DROP FOREIGN KEY FK_4A491FD507FAB6A');
        $this->addSql('DROP INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings');
        $this->addSql('ALTER TABLE VSAPP_Settings CHANGE maintenance_page_id maintenance_page_id  INT DEFAULT NULL');
        $this->addSql('ALTER TABLE VSAPP_Settings ADD CONSTRAINT FK_4A491FD507FAB6A FOREIGN KEY (maintenance_page_id ) REFERENCES VSCMS_Pages (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings (maintenance_page_id )');
        $this->addSql('ALTER TABLE VSUM_UsersInfo CHANGE title title ENUM(\'mr\', \'mrs\', \'miss\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE VVP_VideoCategories DROP FOREIGN KEY FK_2A33723D727ACA70');
        $this->addSql('ALTER TABLE VVP_VideoCategories DROP FOREIGN KEY FK_2A33723DDE13F470');
        $this->addSql('ALTER TABLE VVP_Videos DROP FOREIGN KEY FK_49B70CBAA76ED395');
        $this->addSql('ALTER TABLE VVP_Videos_Categories DROP FOREIGN KEY FK_AB2A07DB29C1004E');
        $this->addSql('ALTER TABLE VVP_Videos_Categories DROP FOREIGN KEY FK_AB2A07DB12469DE2');
        $this->addSql('ALTER TABLE VVP_Videos_Files DROP FOREIGN KEY FK_CC839EBF7E3C61F9');
        $this->addSql('DROP TABLE VVP_VideoCategories');
        $this->addSql('DROP TABLE VVP_Videos');
        $this->addSql('DROP TABLE VVP_Videos_Categories');
        $this->addSql('DROP TABLE VVP_Videos_Files');
        $this->addSql('ALTER TABLE VSAPP_Settings DROP FOREIGN KEY FK_4A491FD507FAB6A');
        $this->addSql('DROP INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings');
        $this->addSql('ALTER TABLE VSAPP_Settings CHANGE maintenance_page_id  maintenance_page_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE VSAPP_Settings ADD CONSTRAINT FK_4A491FD507FAB6A FOREIGN KEY (maintenance_page_id) REFERENCES VSCMS_Pages (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings (maintenance_page_id)');
        $this->addSql('ALTER TABLE VSUM_UsersInfo CHANGE title title VARCHAR(255) DEFAULT NULL');
    }
}
