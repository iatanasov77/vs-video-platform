<?php

declare(strict_types=1);

namespace App\DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250218060958 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE VVP_Videos_Trailers (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, path VARCHAR(255) NOT NULL, original_name VARCHAR(255) DEFAULT \'\' NOT NULL COMMENT \'The Original Name of the File.\', UNIQUE INDEX UNIQ_1A7A60B07E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE VVP_Videos_Trailers ADD CONSTRAINT FK_1A7A60B07E3C61F9 FOREIGN KEY (owner_id) REFERENCES VVP_Videos (id)');
        $this->addSql('ALTER TABLE VSAPP_Settings DROP FOREIGN KEY FK_4A491FD507FAB6A');
        $this->addSql('DROP INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings');
        $this->addSql('ALTER TABLE VSAPP_Settings CHANGE maintenance_page_id maintenance_page_id  INT DEFAULT NULL');
        $this->addSql('ALTER TABLE VSAPP_Settings ADD CONSTRAINT FK_4A491FD507FAB6A FOREIGN KEY (maintenance_page_id ) REFERENCES VSCMS_Pages (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings (maintenance_page_id )');
        $this->addSql('ALTER TABLE VSUM_UsersInfo CHANGE title title ENUM(\'mr\', \'mrs\', \'miss\')');
        $this->addSql('ALTER TABLE VVP_VideoPlatformSettings CHANGE transcoded_video_urls_type transcoded_video_urls_type ENUM(\'symfony_route\', \'cloud_public\', \'cloud_signed\'), CHANGE video_clip_maker video_clip_maker ENUM(\'coconut\', \'ffmpeg\')');
        $this->addSql('ALTER TABLE VVP_VideoPlatformStorages CHANGE storage_type storage_type ENUM(\'coconut\', \'local\' , \'s3\' , \'digitalocean\')');
        $this->addSql('ALTER TABLE VVP_Videos_Files CHANGE storage_type storage_type ENUM(\'coconut\', \'local\' , \'s3\' , \'digitalocean\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE VVP_Videos_Trailers DROP FOREIGN KEY FK_1A7A60B07E3C61F9');
        $this->addSql('DROP TABLE VVP_Videos_Trailers');
        $this->addSql('ALTER TABLE VSAPP_Settings DROP FOREIGN KEY FK_4A491FD507FAB6A');
        $this->addSql('DROP INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings');
        $this->addSql('ALTER TABLE VSAPP_Settings CHANGE maintenance_page_id  maintenance_page_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE VSAPP_Settings ADD CONSTRAINT FK_4A491FD507FAB6A FOREIGN KEY (maintenance_page_id) REFERENCES VSCMS_Pages (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings (maintenance_page_id)');
        $this->addSql('ALTER TABLE VSUM_UsersInfo CHANGE title title VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE VVP_VideoPlatformSettings CHANGE transcoded_video_urls_type transcoded_video_urls_type VARCHAR(255) DEFAULT NULL, CHANGE video_clip_maker video_clip_maker VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE VVP_VideoPlatformStorages CHANGE storage_type storage_type VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE VVP_Videos_Files CHANGE storage_type storage_type VARCHAR(255) DEFAULT NULL');
    }
}
