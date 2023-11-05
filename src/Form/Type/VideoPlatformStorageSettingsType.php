<?php namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\TextType;

class VideoPlatformStorageSettingsType extends AbstractType
{
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        //echo '<pre>'; var_dump( $builder->getData() ); die;
        $builder
            ->add( 'settingsKey', TextType::class, [
                'label'                 => 'vs_vvp.form.video_platform_storage.settings_key',
                'attr'                  => [
                    'placeholder'   => 'vs_vvp.form.video_platform_storage.settings_key'
                ],
                'translation_domain'    => 'VanzVideoPlayer',
                'required'              => false,
            ])
            
            ->add( 'settingsValue', TextType::class, [
                'label'                 => 'vs_vvp.form.video_platform_storage.settings_value',
                'attr'                  => [
                    'placeholder'   => 'vs_vvp.form.video_platform_storage.settings_value'
                ],
                'translation_domain'    => 'VanzVideoPlayer',
                'required'              => false,
            ])
        ;
    }
}