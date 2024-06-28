<?php

declare(strict_types=1);

namespace App\DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240628090638 extends AbstractMigration
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
        $this->addSql('ALTER TABLE VSCMS_SlidersItems ADD actor_id INT DEFAULT NULL, ADD video_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE VSCMS_SlidersItems ADD CONSTRAINT FK_15F6ED3110DAF24A FOREIGN KEY (actor_id) REFERENCES VVP_Actors (id)');
        $this->addSql('ALTER TABLE VSCMS_SlidersItems ADD CONSTRAINT FK_15F6ED3129C1004E FOREIGN KEY (video_id) REFERENCES VVP_Videos (id)');
        $this->addSql('CREATE INDEX IDX_15F6ED3110DAF24A ON VSCMS_SlidersItems (actor_id)');
        $this->addSql('CREATE INDEX IDX_15F6ED3129C1004E ON VSCMS_SlidersItems (video_id)');
        $this->addSql('ALTER TABLE VSUM_UsersInfo CHANGE title title ENUM(\'mr\', \'mrs\', \'miss\')');
        $this->addSql('ALTER TABLE VVP_VideoPlatformSettings CHANGE transcoded_video_urls_type transcoded_video_urls_type ENUM(\'symfony_route\', \'cloud_public\', \'cloud_signed\')');
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
        $this->addSql('ALTER TABLE VSCMS_SlidersItems DROP FOREIGN KEY FK_15F6ED3110DAF24A');
        $this->addSql('ALTER TABLE VSCMS_SlidersItems DROP FOREIGN KEY FK_15F6ED3129C1004E');
        $this->addSql('DROP INDEX IDX_15F6ED3110DAF24A ON VSCMS_SlidersItems');
        $this->addSql('DROP INDEX IDX_15F6ED3129C1004E ON VSCMS_SlidersItems');
        $this->addSql('ALTER TABLE VSCMS_SlidersItems DROP actor_id, DROP video_id');
        $this->addSql('ALTER TABLE VSUM_UsersInfo CHANGE title title VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE VVP_CoconutJobs CHANGE job_data job_data LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE VVP_CoconutSettings CHANGE coconut_output_formats coconut_output_formats LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE VVP_VideoPlatformSettings CHANGE transcoded_video_urls_type transcoded_video_urls_type VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE VVP_VideoPlatformStorages CHANGE storage_type storage_type VARCHAR(255) DEFAULT NULL, CHANGE settings settings LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE VVP_Videos_Files CHANGE storage_type storage_type VARCHAR(255) DEFAULT NULL');
    }
}
