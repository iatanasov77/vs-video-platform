<?php

declare(strict_types=1);

namespace App\DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250307074224 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE VSAPP_Settings DROP FOREIGN KEY FK_4A491FD507FAB6A');
        $this->addSql('DROP INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings');
        $this->addSql('ALTER TABLE VSAPP_Settings CHANGE maintenance_page_id maintenance_page_id  INT DEFAULT NULL');
        $this->addSql('ALTER TABLE VSAPP_Settings ADD CONSTRAINT FK_4A491FD507FAB6A FOREIGN KEY (maintenance_page_id ) REFERENCES VSCMS_Pages (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings (maintenance_page_id )');
        $this->addSql('ALTER TABLE VSUM_Users CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE VSUM_UsersInfo CHANGE title title ENUM(\'mr\', \'mrs\', \'miss\')');
        $this->addSql('ALTER TABLE VVP_VideoPlatformSettings ADD signed_url_expiration ENUM(\'24_hours\', \'duration_x1\', \'duration_x2\', \'duration_x3\'), ADD video_player ENUM(\'plyr\', \'videojs\'), CHANGE transcoded_video_urls_type transcoded_video_urls_type ENUM(\'symfony_route\', \'cloud_public\', \'cloud_signed\'), CHANGE video_clip_maker video_clip_maker ENUM(\'none\', \'coconut\', \'ffmpeg\')');
        $this->addSql('ALTER TABLE VVP_VideoPlatformStorages CHANGE storage_type storage_type ENUM(\'coconut\', \'local\' , \'s3\' , \'digitalocean\')');
        $this->addSql('ALTER TABLE VVP_Videos_Categories ADD CONSTRAINT FK_AB2A07DB29C1004E FOREIGN KEY (video_id) REFERENCES VVP_Videos (id)');
        $this->addSql('ALTER TABLE VVP_Videos_Categories ADD CONSTRAINT FK_AB2A07DB12469DE2 FOREIGN KEY (category_id) REFERENCES VVP_VideoCategories (id)');
        $this->addSql('ALTER TABLE VVP_Videos_Files CHANGE storage_type storage_type ENUM(\'coconut\', \'local\' , \'s3\' , \'digitalocean\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE VSAPP_Settings DROP FOREIGN KEY FK_4A491FD507FAB6A');
        $this->addSql('DROP INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings');
        $this->addSql('ALTER TABLE VSAPP_Settings CHANGE maintenance_page_id  maintenance_page_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE VSAPP_Settings ADD CONSTRAINT FK_4A491FD507FAB6A FOREIGN KEY (maintenance_page_id) REFERENCES VSCMS_Pages (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings (maintenance_page_id)');
        $this->addSql('ALTER TABLE VSUM_Users CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE VSUM_UsersInfo CHANGE title title VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE VVP_VideoPlatformSettings DROP signed_url_expiration, DROP video_player, CHANGE transcoded_video_urls_type transcoded_video_urls_type VARCHAR(255) DEFAULT NULL, CHANGE video_clip_maker video_clip_maker VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE VVP_VideoPlatformStorages CHANGE storage_type storage_type VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE VVP_Videos_Categories DROP FOREIGN KEY FK_AB2A07DB29C1004E');
        $this->addSql('ALTER TABLE VVP_Videos_Categories DROP FOREIGN KEY FK_AB2A07DB12469DE2');
        $this->addSql('ALTER TABLE VVP_Videos_Files CHANGE storage_type storage_type VARCHAR(255) DEFAULT NULL');
    }
}
