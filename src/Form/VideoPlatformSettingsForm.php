<?php namespace App\Form;

use Vankosoft\ApplicationBundle\Form\AbstractForm;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use App\Entity\CoconutSettings;
use App\Entity\VideoPlatformSettings;
use App\Entity\VideoPlatformStorage;

class VideoPlatformSettingsForm extends AbstractForm
{
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        parent::buildForm( $builder, $options );
        
        $builder
            ->add( 'settingsKey', TextType::class, [
                'label'                 => 'vs_vvp.form.video_platform_settings.settings_key',
                'translation_domain'    => 'VanzVideoPlayer',
            ])
            
            ->add( 'coconutSettings', EntityType::class, [
                'label'                 => 'vs_vvp.form.video_platform_settings.coconut_settings',
                'translation_domain'    => 'VanzVideoPlayer',
                'class'                 => CoconutSettings::class,
                'choice_label'          => 'title',
                'placeholder'           => 'vs_vvp.form.video_platform_settings.coconut_settings_placeholder'
            ])
            
            ->add( 'originalVideosStorage', EntityType::class, [
                'label'                 => 'vs_vvp.form.video_platform_settings.original_videos_storage',
                'translation_domain'    => 'VanzVideoPlayer',
                'class'                 => VideoPlatformStorage::class,
                'choice_label'          => 'title',
                'placeholder'           => 'vs_vvp.form.video_platform_settings.original_videos_storage_placeholder'
            ])
            
            ->add( 'useFFMpeg', CheckboxType::class, [
                'required'              => false,
                
                'label'                 => 'vs_vvp.form.video_platform_settings.use_ffmpeg',
                'translation_domain'    => 'VanzVideoPlayer',
            ])
            
            ->add( 'displayOnlyTranscoded', CheckboxType::class, [
                'required'              => false,
                
                'label'                 => 'vs_vvp.form.video_platform_settings.display_only_transcoded',
                'translation_domain'    => 'VanzVideoPlayer',
            ])
            
            ->add( 'createUserSignedVideos', CheckboxType::class, [
                'required'              => false,
                
                'label'                 => 'vs_vvp.form.video_platform_settings.create_user_signed_videos',
                'translation_domain'    => 'VanzVideoPlayer',
            ])
            
            ->add( 'disableVideosForNonAuthenticated', CheckboxType::class, [
                'required'              => false,
                
                'label'                 => 'vs_vvp.form.video_platform_settings.disable_videos_for_non_authenticated',
                'translation_domain'    => 'VanzVideoPlayer',
            ])
        ;
    }
    
    public function configureOptions( OptionsResolver $resolver ): void
    {
        parent::configureOptions( $resolver );
        
        $resolver
            ->setDefaults([
                'data_class'            => VideoPlatformSettings::class,
                'csrf_protection'       => false,
            ])
        ;
    }
    
    public function getName()
    {
        return 'vs_vvp.video_platform_settings';
    }
}