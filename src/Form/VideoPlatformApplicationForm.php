<?php namespace App\Form;

use Vankosoft\ApplicationBundle\Form\AbstractForm;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use App\Entity\VideoPlatformApplication;
use App\Entity\VideoPlatformSettings;

class VideoPlatformApplicationForm extends AbstractForm
{
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        parent::buildForm( $builder, $options );
        
        $builder
            ->add( 'applicationCode', HiddenType::class, ['mapped'    => false] )
            
            ->add( 'settings', EntityType::class, [
                'label'                 => 'vs_vvp.form.video_platform_settings_label',
                'translation_domain'    => 'VanzVideoPlayer',
                'placeholder'           => 'vs_vvp.form.video_platform_settings_placeholder',
                'class'                 => VideoPlatformSettings::class,
                'choice_label'          => 'settingsKey',
            ])
        ;
    }
    
    public function configureOptions( OptionsResolver $resolver ): void
    {
        parent::configureOptions( $resolver );
        
        $resolver
            ->setDefaults([
                'data_class'        => VideoPlatformApplication::class,
                'csrf_protection'   => false,
            ])
        ;
    }
    
    public function getName()
    {
        return 'vs_vvp.video_platform_application';
    }
}