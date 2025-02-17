<?php namespace App\Form;

use Vankosoft\ApplicationBundle\Form\AbstractForm;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use App\Component\VideoUrlsFactory;
use App\Component\VideoClipMaker;
use App\Entity\CoconutSettings;
use App\Entity\VideoPlatformSettings;
use App\Entity\VideoPlatformStorage;
use App\Entity\Catalog\AssociationType;

class VideoPlatformSettingsForm extends AbstractForm
{
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        parent::buildForm( $builder, $options );
        
        $entity = $builder->getData();
        
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
            
            ->add( 'videoSuggestionsAssociationType', EntityType::class, [
                'required'              => false,
                'label'                 => 'vs_vvp.form.video_platform_settings.video_suggestions_strategy',
                'translation_domain'    => 'VanzVideoPlayer',
                'class'                 => AssociationType::class,
                'choice_label'          => 'name',
                'placeholder'           => 'vs_vvp.form.video_platform_settings.video_suggestions_strategy_placeholder'
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
            
            ->add( 'disableVideosForNonAuthenticated', CheckboxType::class, [
                'required'              => false,
                
                'label'                 => 'vs_vvp.form.video_platform_settings.disable_videos_for_non_authenticated',
                'translation_domain'    => 'VanzVideoPlayer',
            ])
            
            ->add( 'transcodedVideoUrlsType', ChoiceType::class, [
                'label'                 => 'vs_vvp.form.video_platform_settings.video_player_transcoded_urls',
                'translation_domain'    => 'VanzVideoPlayer',
                
                'choices'               => \array_flip( VideoUrlsFactory::VIDEO_URL_TYPES ),
                'expanded'              => true,
                'data'                  => empty( $entity->getTranscodedVideoUrlsType() ) ?
                                            \array_key_first( VideoUrlsFactory::VIDEO_URL_TYPES ) :
                                            $entity->getTranscodedVideoUrlsType(),
            ])
            
            ->add( 'videoClipMaker', ChoiceType::class, [
                'label'                 => 'vs_vvp.form.video_platform_settings.video_clip_maker',
                'translation_domain'    => 'VanzVideoPlayer',
                
                'choices'               => \array_flip( VideoClipMaker::VIDEO_CLIP_MAKERS ),
                'expanded'              => true,
                'data'                  => empty( $entity->getVideoClipMaker() ) ?
                                            \array_key_first( VideoClipMaker::VIDEO_CLIP_MAKERS ) :
                                            $entity->getVideoClipMaker(),
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