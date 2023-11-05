<?php

declare(strict_types=1);

namespace App\DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231103095533 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE VVP_CoconutSettings (id INT AUTO_INCREMENT NOT NULL, storage_id INT NOT NULL, title VARCHAR(64) NOT NULL, coconut_api_key VARCHAR(255) NOT NULL, coconut_output_formats LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', coconut_system_user VARCHAR(32) NOT NULL, coconut_system_password VARCHAR(32) NOT NULL, coconut_input_url_type VARCHAR(32) NOT NULL, coconut_watermark TINYINT(1) DEFAULT 0 NOT NULL, INDEX IDX_A1C4C5875CC5DB90 (storage_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE VVP_CoconutSettings ADD CONSTRAINT FK_A1C4C5875CC5DB90 FOREIGN KEY (storage_id) REFERENCES VVP_VideoPlatformStorages (id)');
        $this->addSql('ALTER TABLE VSAPP_Settings DROP FOREIGN KEY FK_4A491FD507FAB6A');
        $this->addSql('DROP INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings');
        $this->addSql('ALTER TABLE VSAPP_Settings CHANGE maintenance_page_id maintenance_page_id  INT DEFAULT NULL');
        $this->addSql('ALTER TABLE VSAPP_Settings ADD CONSTRAINT FK_4A491FD507FAB6A FOREIGN KEY (maintenance_page_id ) REFERENCES VSCMS_Pages (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings (maintenance_page_id )');
        $this->addSql('ALTER TABLE VSPAY_Order CHANGE status status ENUM(\'shopping_cart\', \'paid_order\', \'failed_order\')');
        $this->addSql('ALTER TABLE VSUM_UsersInfo CHANGE title title ENUM(\'mr\', \'mrs\', \'miss\')');
        $this->addSql('ALTER TABLE VVP_VideoPlatformSettings DROP FOREIGN KEY FK_1FC223C85CC5DB90');
        $this->addSql('ALTER TABLE VVP_VideoPlatformSettings DROP FOREIGN KEY FK_1FC223C83E030ACD');
        $this->addSql('DROP INDEX IDX_1FC223C83E030ACD ON VVP_VideoPlatformSettings');
        $this->addSql('DROP INDEX IDX_1FC223C85CC5DB90 ON VVP_VideoPlatformSettings');
        $this->addSql('ALTER TABLE VVP_VideoPlatformSettings ADD original_videos_storage_id INT DEFAULT NULL, DROP storage_id, DROP coconut_api_key, DROP coconut_output_formats, DROP coconut_system_user, DROP coconut_system_password, DROP coconut_input_url_type, DROP coconut_watermark, CHANGE application_id coconut_settings_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE VVP_VideoPlatformSettings ADD CONSTRAINT FK_1FC223C853822540 FOREIGN KEY (coconut_settings_id) REFERENCES VVP_CoconutSettings (id)');
        $this->addSql('ALTER TABLE VVP_VideoPlatformSettings ADD CONSTRAINT FK_1FC223C8663C98BC FOREIGN KEY (original_videos_storage_id) REFERENCES VVP_VideoPlatformStorages (id)');
        $this->addSql('CREATE INDEX IDX_1FC223C853822540 ON VVP_VideoPlatformSettings (coconut_settings_id)');
        $this->addSql('CREATE INDEX IDX_1FC223C8663C98BC ON VVP_VideoPlatformSettings (original_videos_storage_id)');
        $this->addSql('ALTER TABLE VVP_VideoPlatformStorages CHANGE storage_type storage_type ENUM(\'coconut\', \'local\' , \'s3\' , \'digitalocean\')');
        $this->addSql('ALTER TABLE VVP_Videos_Files CHANGE storage_type storage_type ENUM(\'coconut\', \'local\' , \'s3\' , \'digitalocean\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE VVP_VideoPlatformSettings DROP FOREIGN KEY FK_1FC223C853822540');
        $this->addSql('ALTER TABLE VVP_CoconutSettings DROP FOREIGN KEY FK_A1C4C5875CC5DB90');
        $this->addSql('DROP TABLE VVP_CoconutSettings');
        $this->addSql('ALTER TABLE VSAPP_Settings DROP FOREIGN KEY FK_4A491FD507FAB6A');
        $this->addSql('DROP INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings');
        $this->addSql('ALTER TABLE VSAPP_Settings CHANGE maintenance_page_id  maintenance_page_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE VSAPP_Settings ADD CONSTRAINT FK_4A491FD507FAB6A FOREIGN KEY (maintenance_page_id) REFERENCES VSCMS_Pages (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings (maintenance_page_id)');
        $this->addSql('ALTER TABLE VSPAY_Order CHANGE status status VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE VSUM_UsersInfo CHANGE title title VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE VVP_VideoPlatformSettings DROP FOREIGN KEY FK_1FC223C8663C98BC');
        $this->addSql('DROP INDEX IDX_1FC223C853822540 ON VVP_VideoPlatformSettings');
        $this->addSql('DROP INDEX IDX_1FC223C8663C98BC ON VVP_VideoPlatformSettings');
        $this->addSql('ALTER TABLE VVP_VideoPlatformSettings ADD storage_id INT NOT NULL, ADD application_id INT DEFAULT NULL, ADD coconut_api_key VARCHAR(255) NOT NULL, ADD coconut_output_formats LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', ADD coconut_system_user VARCHAR(32) NOT NULL, ADD coconut_system_password VARCHAR(32) NOT NULL, ADD coconut_input_url_type VARCHAR(32) NOT NULL, ADD coconut_watermark TINYINT(1) DEFAULT 0 NOT NULL, DROP coconut_settings_id, DROP original_videos_storage_id');
        $this->addSql('ALTER TABLE VVP_VideoPlatformSettings ADD CONSTRAINT FK_1FC223C85CC5DB90 FOREIGN KEY (storage_id) REFERENCES VVP_VideoPlatformStorages (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE VVP_VideoPlatformSettings ADD CONSTRAINT FK_1FC223C83E030ACD FOREIGN KEY (application_id) REFERENCES VSAPP_Applications (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_1FC223C83E030ACD ON VVP_VideoPlatformSettings (application_id)');
        $this->addSql('CREATE INDEX IDX_1FC223C85CC5DB90 ON VVP_VideoPlatformSettings (storage_id)');
        $this->addSql('ALTER TABLE VVP_VideoPlatformStorages CHANGE storage_type storage_type VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE VVP_Videos_Files CHANGE storage_type storage_type VARCHAR(255) DEFAULT NULL');
    }
}
