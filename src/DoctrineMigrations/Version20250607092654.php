<?php

declare(strict_types=1);

namespace App\DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250607092654 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE VSAPI_RefreshTokens (id INT AUTO_INCREMENT NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid DATETIME NOT NULL, jobs_finished JSON NOT NULL, UNIQUE INDEX UNIQ_BB25E413C74F2195 (refresh_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE VVP_ActorReviews (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, subject_id INT NOT NULL, title VARCHAR(255) DEFAULT NULL, comment LONGTEXT DEFAULT NULL, rating INT DEFAULT NULL, status VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_19BD08FDF675F31B (author_id), INDEX IDX_19BD08FD23EDC87 (subject_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE VVP_Actors (id INT AUTO_INCREMENT NOT NULL, slug VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, career VARCHAR(255) DEFAULT NULL, height VARCHAR(8) DEFAULT NULL, date_of_birth DATETIME DEFAULT NULL, place_of_birth VARCHAR(255) DEFAULT NULL, average_rating DOUBLE PRECISION DEFAULT '0' NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_BF36986D989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE VVP_Actors_Genres (actor_id INT NOT NULL, genre_id INT NOT NULL, INDEX IDX_2650AD4210DAF24A (actor_id), INDEX IDX_2650AD424296D31F (genre_id), PRIMARY KEY(actor_id, genre_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE VVP_Actors_Photos (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, path VARCHAR(255) NOT NULL, original_name VARCHAR(255) DEFAULT '' NOT NULL COMMENT 'The Original Name of the File.', INDEX IDX_86CDA88D7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE VVP_CoconutClipJobs (id INT AUTO_INCREMENT NOT NULL, video_id INT DEFAULT NULL, job_id VARCHAR(32) NOT NULL, job_data JSON DEFAULT NULL, status VARCHAR(32) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_CC1D985829C1004E (video_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE VVP_CoconutSettings (id INT AUTO_INCREMENT NOT NULL, storage_id INT NOT NULL, title VARCHAR(64) NOT NULL, coconut_api_key VARCHAR(255) NOT NULL, coconut_output_formats JSON NOT NULL, coconut_system_user VARCHAR(32) NOT NULL, coconut_system_password VARCHAR(32) NOT NULL, coconut_input_url_type VARCHAR(32) NOT NULL, coconut_watermark TINYINT(1) DEFAULT 0 NOT NULL, coconut_clip_format VARCHAR(16) DEFAULT NULL, coconut_clip_offset INT DEFAULT NULL, coconut_clip_duration INT DEFAULT NULL, coconut_clip_output_url VARCHAR(255) DEFAULT NULL, INDEX IDX_A1C4C5875CC5DB90 (storage_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE VVP_CoconutVideoJobs (id INT AUTO_INCREMENT NOT NULL, video_id INT DEFAULT NULL, job_id VARCHAR(32) NOT NULL, job_data JSON DEFAULT NULL, status VARCHAR(32) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_27C5BFDC29C1004E (video_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE VVP_GoogleCloudProjects (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(64) NOT NULL, slug VARCHAR(255) NOT NULL, google_api_key VARCHAR(255) NOT NULL, google_client_id VARCHAR(255) NOT NULL, google_client_secret VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_CBCDE787989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE VVP_VideoCategories (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, taxon_id INT DEFAULT NULL, INDEX IDX_2A33723D727ACA70 (parent_id), UNIQUE INDEX UNIQ_2A33723DDE13F470 (taxon_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE VVP_VideoComments (id INT AUTO_INCREMENT NOT NULL, tree_root INT DEFAULT NULL, parent_id INT DEFAULT NULL, author_id INT DEFAULT NULL, subject_id INT NOT NULL, tree_level INT NOT NULL, tree_left INT NOT NULL, tree_right INT NOT NULL, comment LONGTEXT NOT NULL, likes INT DEFAULT 0 NOT NULL, dislikes INT DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_CB45713EA977936C (tree_root), INDEX IDX_CB45713E727ACA70 (parent_id), INDEX IDX_CB45713EF675F31B (author_id), INDEX IDX_CB45713E23EDC87 (subject_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE VVP_VideoComment_UserLikes (comment_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_932A02B1F8697D13 (comment_id), INDEX IDX_932A02B1A76ED395 (user_id), PRIMARY KEY(comment_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE VVP_VideoComment_UserDislikes (comment_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_4980339EF8697D13 (comment_id), INDEX IDX_4980339EA76ED395 (user_id), PRIMARY KEY(comment_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE VVP_VideoGenres (id INT AUTO_INCREMENT NOT NULL, taxon_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_A41A7A25DE13F470 (taxon_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE VVP_VideoPlatformApplications (id INT AUTO_INCREMENT NOT NULL, application_id INT DEFAULT NULL, settings_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_CF7A0E7A3E030ACD (application_id), INDEX IDX_CF7A0E7A59949888 (settings_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE VVP_VideoPlatformSettings (id INT AUTO_INCREMENT NOT NULL, coconut_settings_id INT DEFAULT NULL, original_videos_storage_id INT DEFAULT NULL, video_suggestions_association_type_id INT DEFAULT NULL, settings_key VARCHAR(32) NOT NULL, use_ffmpeg TINYINT(1) DEFAULT 0 NOT NULL, display_only_transcoded TINYINT(1) DEFAULT 0 NOT NULL, disable_videos_for_non_authenticated TINYINT(1) DEFAULT 0 NOT NULL, transcoded_video_urls_type ENUM('symfony_route', 'cloud_public', 'cloud_signed'), signed_url_expiration ENUM('24_hours', 'duration_x1', 'duration_x2', 'duration_x3'), user_sign_with ENUM('none', 'username', 'email', 'fullname'), video_clip_maker ENUM('none', 'coconut', 'ffmpeg'), use_onhover_player TINYINT(1) DEFAULT 0 NOT NULL, video_player ENUM('plyr', 'videojs'), INDEX IDX_1FC223C853822540 (coconut_settings_id), INDEX IDX_1FC223C8663C98BC (original_videos_storage_id), INDEX IDX_1FC223C81A729044 (video_suggestions_association_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE VVP_VideoPlatformStorages (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(64) NOT NULL, storage_type ENUM('coconut', 'local' , 's3' , 'digitalocean'), settings JSON NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE VVP_VideoReviews (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, subject_id INT NOT NULL, title VARCHAR(255) DEFAULT NULL, comment LONGTEXT DEFAULT NULL, rating INT DEFAULT NULL, status VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_D6AC7B86F675F31B (author_id), INDEX IDX_D6AC7B8623EDC87 (subject_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE VVP_Videos (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, published TINYINT(1) NOT NULL, slug VARCHAR(255) NOT NULL, name VARCHAR(64) NOT NULL, description LONGTEXT DEFAULT NULL, in_stock INT DEFAULT 0 NOT NULL, tags VARCHAR(255) DEFAULT '', created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, average_rating DOUBLE PRECISION DEFAULT '0' NOT NULL, UNIQUE INDEX UNIQ_49B70CBA989D9B62 (slug), INDEX IDX_49B70CBAA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE VVP_Videos_Categories (video_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_AB2A07DB29C1004E (video_id), INDEX IDX_AB2A07DB12469DE2 (category_id), PRIMARY KEY(video_id, category_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE VVP_Videos_Genres (video_id INT NOT NULL, genre_id INT NOT NULL, INDEX IDX_E148148529C1004E (video_id), INDEX IDX_E14814854296D31F (genre_id), PRIMARY KEY(video_id, genre_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE VVP_Videos_Actors (video_id INT NOT NULL, actor_id INT NOT NULL, INDEX IDX_9688017629C1004E (video_id), INDEX IDX_9688017610DAF24A (actor_id), PRIMARY KEY(video_id, actor_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE VVP_Videos_PaidServices (video_id INT NOT NULL, paid_service_id INT NOT NULL, INDEX IDX_C3EDB08329C1004E (video_id), INDEX IDX_C3EDB08387FFD8A7 (paid_service_id), PRIMARY KEY(video_id, paid_service_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE VVP_Videos_UsersWatched (video_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_B1011ED629C1004E (video_id), INDEX IDX_B1011ED6A76ED395 (user_id), PRIMARY KEY(video_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE VVP_Videos_Clips (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, path VARCHAR(255) NOT NULL, original_name VARCHAR(255) DEFAULT '' NOT NULL COMMENT 'The Original Name of the File.', UNIQUE INDEX UNIQ_2C3C5027E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE VVP_Videos_Files (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, path VARCHAR(255) NOT NULL, original_name VARCHAR(255) DEFAULT '' NOT NULL COMMENT 'The Original Name of the File.', storage_type ENUM('coconut', 'local' , 's3' , 'digitalocean'), duration VARCHAR(255) DEFAULT '0' NOT NULL, UNIQUE INDEX UNIQ_CC839EBF7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE VVP_Videos_Photos (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, path VARCHAR(255) NOT NULL, original_name VARCHAR(255) DEFAULT '' NOT NULL COMMENT 'The Original Name of the File.', code VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, INDEX IDX_41D5114A7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE VVP_Videos_Trailers (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, path VARCHAR(255) NOT NULL, original_name VARCHAR(255) DEFAULT '' NOT NULL COMMENT 'The Original Name of the File.', UNIQUE INDEX UNIQ_1A7A60B07E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE VVP_YoutubeChannels (id INT AUTO_INCREMENT NOT NULL, project_id INT DEFAULT NULL, title VARCHAR(64) NOT NULL, slug VARCHAR(255) NOT NULL, channel_id VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_E8EDA74989D9B62 (slug), INDEX IDX_E8EDA74166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE VVP_YoutubeChannels_Photos (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, path VARCHAR(255) NOT NULL, original_name VARCHAR(255) DEFAULT '' NOT NULL COMMENT 'The Original Name of the File.', INDEX IDX_B362B0DD7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_ActorReviews ADD CONSTRAINT FK_19BD08FDF675F31B FOREIGN KEY (author_id) REFERENCES VSUM_Users (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_ActorReviews ADD CONSTRAINT FK_19BD08FD23EDC87 FOREIGN KEY (subject_id) REFERENCES VVP_Actors (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_Actors_Genres ADD CONSTRAINT FK_2650AD4210DAF24A FOREIGN KEY (actor_id) REFERENCES VVP_Actors (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_Actors_Genres ADD CONSTRAINT FK_2650AD424296D31F FOREIGN KEY (genre_id) REFERENCES VVP_VideoGenres (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_Actors_Photos ADD CONSTRAINT FK_86CDA88D7E3C61F9 FOREIGN KEY (owner_id) REFERENCES VVP_Actors (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_CoconutClipJobs ADD CONSTRAINT FK_CC1D985829C1004E FOREIGN KEY (video_id) REFERENCES VVP_Videos (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_CoconutSettings ADD CONSTRAINT FK_A1C4C5875CC5DB90 FOREIGN KEY (storage_id) REFERENCES VVP_VideoPlatformStorages (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_CoconutVideoJobs ADD CONSTRAINT FK_27C5BFDC29C1004E FOREIGN KEY (video_id) REFERENCES VVP_Videos (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_VideoCategories ADD CONSTRAINT FK_2A33723D727ACA70 FOREIGN KEY (parent_id) REFERENCES VVP_VideoCategories (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_VideoCategories ADD CONSTRAINT FK_2A33723DDE13F470 FOREIGN KEY (taxon_id) REFERENCES VSAPP_Taxons (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_VideoComments ADD CONSTRAINT FK_CB45713EA977936C FOREIGN KEY (tree_root) REFERENCES VVP_VideoComments (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_VideoComments ADD CONSTRAINT FK_CB45713E727ACA70 FOREIGN KEY (parent_id) REFERENCES VVP_VideoComments (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_VideoComments ADD CONSTRAINT FK_CB45713EF675F31B FOREIGN KEY (author_id) REFERENCES VSUM_Users (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_VideoComments ADD CONSTRAINT FK_CB45713E23EDC87 FOREIGN KEY (subject_id) REFERENCES VVP_Videos (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_VideoComment_UserLikes ADD CONSTRAINT FK_932A02B1F8697D13 FOREIGN KEY (comment_id) REFERENCES VVP_VideoComments (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_VideoComment_UserLikes ADD CONSTRAINT FK_932A02B1A76ED395 FOREIGN KEY (user_id) REFERENCES VSUM_Users (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_VideoComment_UserDislikes ADD CONSTRAINT FK_4980339EF8697D13 FOREIGN KEY (comment_id) REFERENCES VVP_VideoComments (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_VideoComment_UserDislikes ADD CONSTRAINT FK_4980339EA76ED395 FOREIGN KEY (user_id) REFERENCES VSUM_Users (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_VideoGenres ADD CONSTRAINT FK_A41A7A25DE13F470 FOREIGN KEY (taxon_id) REFERENCES VSAPP_Taxons (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_VideoPlatformApplications ADD CONSTRAINT FK_CF7A0E7A3E030ACD FOREIGN KEY (application_id) REFERENCES VSAPP_Applications (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_VideoPlatformApplications ADD CONSTRAINT FK_CF7A0E7A59949888 FOREIGN KEY (settings_id) REFERENCES VVP_VideoPlatformSettings (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_VideoPlatformSettings ADD CONSTRAINT FK_1FC223C853822540 FOREIGN KEY (coconut_settings_id) REFERENCES VVP_CoconutSettings (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_VideoPlatformSettings ADD CONSTRAINT FK_1FC223C8663C98BC FOREIGN KEY (original_videos_storage_id) REFERENCES VVP_VideoPlatformStorages (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_VideoPlatformSettings ADD CONSTRAINT FK_1FC223C81A729044 FOREIGN KEY (video_suggestions_association_type_id) REFERENCES VSCAT_AssociationTypes (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_VideoReviews ADD CONSTRAINT FK_D6AC7B86F675F31B FOREIGN KEY (author_id) REFERENCES VSUM_Users (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_VideoReviews ADD CONSTRAINT FK_D6AC7B8623EDC87 FOREIGN KEY (subject_id) REFERENCES VVP_Videos (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_Videos ADD CONSTRAINT FK_49B70CBAA76ED395 FOREIGN KEY (user_id) REFERENCES VSUM_Users (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_Videos_Categories ADD CONSTRAINT FK_AB2A07DB29C1004E FOREIGN KEY (video_id) REFERENCES VVP_Videos (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_Videos_Categories ADD CONSTRAINT FK_AB2A07DB12469DE2 FOREIGN KEY (category_id) REFERENCES VVP_VideoCategories (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_Videos_Genres ADD CONSTRAINT FK_E148148529C1004E FOREIGN KEY (video_id) REFERENCES VVP_Videos (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_Videos_Genres ADD CONSTRAINT FK_E14814854296D31F FOREIGN KEY (genre_id) REFERENCES VVP_VideoGenres (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_Videos_Actors ADD CONSTRAINT FK_9688017629C1004E FOREIGN KEY (video_id) REFERENCES VVP_Videos (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_Videos_Actors ADD CONSTRAINT FK_9688017610DAF24A FOREIGN KEY (actor_id) REFERENCES VVP_Actors (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_Videos_PaidServices ADD CONSTRAINT FK_C3EDB08329C1004E FOREIGN KEY (video_id) REFERENCES VVP_Videos (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_Videos_PaidServices ADD CONSTRAINT FK_C3EDB08387FFD8A7 FOREIGN KEY (paid_service_id) REFERENCES VSUS_PayedServices (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_Videos_UsersWatched ADD CONSTRAINT FK_B1011ED629C1004E FOREIGN KEY (video_id) REFERENCES VVP_Videos (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_Videos_UsersWatched ADD CONSTRAINT FK_B1011ED6A76ED395 FOREIGN KEY (user_id) REFERENCES VSUM_Users (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_Videos_Clips ADD CONSTRAINT FK_2C3C5027E3C61F9 FOREIGN KEY (owner_id) REFERENCES VVP_Videos (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_Videos_Files ADD CONSTRAINT FK_CC839EBF7E3C61F9 FOREIGN KEY (owner_id) REFERENCES VVP_Videos (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_Videos_Photos ADD CONSTRAINT FK_41D5114A7E3C61F9 FOREIGN KEY (owner_id) REFERENCES VVP_Videos (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_Videos_Trailers ADD CONSTRAINT FK_1A7A60B07E3C61F9 FOREIGN KEY (owner_id) REFERENCES VVP_Videos (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_YoutubeChannels ADD CONSTRAINT FK_E8EDA74166D1F9C FOREIGN KEY (project_id) REFERENCES VVP_GoogleCloudProjects (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_YoutubeChannels_Photos ADD CONSTRAINT FK_B362B0DD7E3C61F9 FOREIGN KEY (owner_id) REFERENCES VVP_YoutubeChannels (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VSAPP_Settings DROP FOREIGN KEY FK_4A491FD507FAB6A
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VSAPP_Settings CHANGE maintenance_page_id maintenance_page_id  INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VSAPP_Settings ADD CONSTRAINT FK_4A491FD507FAB6A FOREIGN KEY (maintenance_page_id ) REFERENCES VSCMS_Pages (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings (maintenance_page_id )
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VSCMS_SlidersItems ADD actor_id INT DEFAULT NULL, ADD video_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VSCMS_SlidersItems ADD CONSTRAINT FK_15F6ED3110DAF24A FOREIGN KEY (actor_id) REFERENCES VVP_Actors (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VSCMS_SlidersItems ADD CONSTRAINT FK_15F6ED3129C1004E FOREIGN KEY (video_id) REFERENCES VVP_Videos (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_15F6ED3110DAF24A ON VSCMS_SlidersItems (actor_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_15F6ED3129C1004E ON VSCMS_SlidersItems (video_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VSUM_UsersInfo CHANGE title title ENUM('mr', 'mrs', 'miss')
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE VSCMS_SlidersItems DROP FOREIGN KEY FK_15F6ED3110DAF24A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VSCMS_SlidersItems DROP FOREIGN KEY FK_15F6ED3129C1004E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_ActorReviews DROP FOREIGN KEY FK_19BD08FDF675F31B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_ActorReviews DROP FOREIGN KEY FK_19BD08FD23EDC87
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_Actors_Genres DROP FOREIGN KEY FK_2650AD4210DAF24A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_Actors_Genres DROP FOREIGN KEY FK_2650AD424296D31F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_Actors_Photos DROP FOREIGN KEY FK_86CDA88D7E3C61F9
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_CoconutClipJobs DROP FOREIGN KEY FK_CC1D985829C1004E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_CoconutSettings DROP FOREIGN KEY FK_A1C4C5875CC5DB90
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_CoconutVideoJobs DROP FOREIGN KEY FK_27C5BFDC29C1004E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_VideoCategories DROP FOREIGN KEY FK_2A33723D727ACA70
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_VideoCategories DROP FOREIGN KEY FK_2A33723DDE13F470
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_VideoComments DROP FOREIGN KEY FK_CB45713EA977936C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_VideoComments DROP FOREIGN KEY FK_CB45713E727ACA70
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_VideoComments DROP FOREIGN KEY FK_CB45713EF675F31B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_VideoComments DROP FOREIGN KEY FK_CB45713E23EDC87
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_VideoComment_UserLikes DROP FOREIGN KEY FK_932A02B1F8697D13
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_VideoComment_UserLikes DROP FOREIGN KEY FK_932A02B1A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_VideoComment_UserDislikes DROP FOREIGN KEY FK_4980339EF8697D13
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_VideoComment_UserDislikes DROP FOREIGN KEY FK_4980339EA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_VideoGenres DROP FOREIGN KEY FK_A41A7A25DE13F470
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_VideoPlatformApplications DROP FOREIGN KEY FK_CF7A0E7A3E030ACD
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_VideoPlatformApplications DROP FOREIGN KEY FK_CF7A0E7A59949888
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_VideoPlatformSettings DROP FOREIGN KEY FK_1FC223C853822540
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_VideoPlatformSettings DROP FOREIGN KEY FK_1FC223C8663C98BC
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_VideoPlatformSettings DROP FOREIGN KEY FK_1FC223C81A729044
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_VideoReviews DROP FOREIGN KEY FK_D6AC7B86F675F31B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_VideoReviews DROP FOREIGN KEY FK_D6AC7B8623EDC87
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_Videos DROP FOREIGN KEY FK_49B70CBAA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_Videos_Categories DROP FOREIGN KEY FK_AB2A07DB29C1004E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_Videos_Categories DROP FOREIGN KEY FK_AB2A07DB12469DE2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_Videos_Genres DROP FOREIGN KEY FK_E148148529C1004E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_Videos_Genres DROP FOREIGN KEY FK_E14814854296D31F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_Videos_Actors DROP FOREIGN KEY FK_9688017629C1004E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_Videos_Actors DROP FOREIGN KEY FK_9688017610DAF24A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_Videos_PaidServices DROP FOREIGN KEY FK_C3EDB08329C1004E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_Videos_PaidServices DROP FOREIGN KEY FK_C3EDB08387FFD8A7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_Videos_UsersWatched DROP FOREIGN KEY FK_B1011ED629C1004E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_Videos_UsersWatched DROP FOREIGN KEY FK_B1011ED6A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_Videos_Clips DROP FOREIGN KEY FK_2C3C5027E3C61F9
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_Videos_Files DROP FOREIGN KEY FK_CC839EBF7E3C61F9
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_Videos_Photos DROP FOREIGN KEY FK_41D5114A7E3C61F9
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_Videos_Trailers DROP FOREIGN KEY FK_1A7A60B07E3C61F9
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_YoutubeChannels DROP FOREIGN KEY FK_E8EDA74166D1F9C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VVP_YoutubeChannels_Photos DROP FOREIGN KEY FK_B362B0DD7E3C61F9
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE VSAPI_RefreshTokens
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE VVP_ActorReviews
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE VVP_Actors
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE VVP_Actors_Genres
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE VVP_Actors_Photos
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE VVP_CoconutClipJobs
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE VVP_CoconutSettings
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE VVP_CoconutVideoJobs
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE VVP_GoogleCloudProjects
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE VVP_VideoCategories
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE VVP_VideoComments
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE VVP_VideoComment_UserLikes
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE VVP_VideoComment_UserDislikes
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE VVP_VideoGenres
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE VVP_VideoPlatformApplications
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE VVP_VideoPlatformSettings
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE VVP_VideoPlatformStorages
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE VVP_VideoReviews
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE VVP_Videos
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE VVP_Videos_Categories
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE VVP_Videos_Genres
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE VVP_Videos_Actors
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE VVP_Videos_PaidServices
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE VVP_Videos_UsersWatched
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE VVP_Videos_Clips
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE VVP_Videos_Files
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE VVP_Videos_Photos
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE VVP_Videos_Trailers
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE VVP_YoutubeChannels
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE VVP_YoutubeChannels_Photos
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VSAPP_Settings DROP FOREIGN KEY FK_4A491FD507FAB6A
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VSAPP_Settings CHANGE maintenance_page_id  maintenance_page_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VSAPP_Settings ADD CONSTRAINT FK_4A491FD507FAB6A FOREIGN KEY (maintenance_page_id) REFERENCES VSCMS_Pages (id) ON UPDATE NO ACTION ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings (maintenance_page_id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_15F6ED3110DAF24A ON VSCMS_SlidersItems
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_15F6ED3129C1004E ON VSCMS_SlidersItems
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VSCMS_SlidersItems DROP actor_id, DROP video_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE VSUM_UsersInfo CHANGE title title VARCHAR(255) DEFAULT NULL
        SQL);
    }
}
