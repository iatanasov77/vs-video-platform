<?php namespace App\Form;

use Vankosoft\ApplicationBundle\Form\AbstractForm;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use App\Form\DataTransformer\CoconutOutputFormatsTransformer;
use App\Form\Type\CoconutOutputFormatType;

use App\Entity\CoconutSettings;
use App\Entity\VideoPlatformStorage;
use App\Component\VideoPlatform;

class CoconutSettingsForm extends AbstractForm
{
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        parent::buildForm( $builder, $options );
        
        $entity = $builder->getData();
        //var_dump( $entity->getCoconutOutputFormats() ); die;
        
        $builder
            ->add( 'title', TextType::class, [
                'label'                 => 'vs_vvp.form.video_platform_settings.coconut_settings_title',
                'translation_domain'    => 'VanzVideoPlayer',
            ])
        
            ->add( 'coconutApiKey', TextType::class, [
                'label'                 => 'vs_vvp.form.video_platform_settings.coconut_api_key',
                'translation_domain'    => 'VanzVideoPlayer',
            ])
            
            ->add( 'coconutStorage', EntityType::class, [
                'label'                 => 'vs_vvp.form.video_platform_settings.coconut_storage',
                'translation_domain'    => 'VanzVideoPlayer',
                'class'                 => VideoPlatformStorage::class,
                'choice_label'          => 'title',
                'placeholder'           => 'vs_vvp.form.video_platform_settings.coconut_storage_placeholder'
            ])
            
            ->add( 'coconutWatermark', CheckboxType::class, [
                'required'              => false,
                
                'label'                 => 'vs_vvp.form.video_platform_settings.coconut_set_watermark',
                'translation_domain'    => 'VanzVideoPlayer',
            ])
            
            ->add( 'coconutOutputFormats', CollectionType::class, [
                'entry_type'    => CoconutOutputFormatType::class,
                'allow_add'     => true,
                'allow_delete'  => true,
                'prototype'     => true,
                'by_reference'  => false,
            ])
            
            ->add( 'coconutSystemUser', TextType::class, [
                'label'                 => 'vs_vvp.form.video_platform_settings.coconut_system_user',
                'translation_domain'    => 'VanzVideoPlayer',
            ])
            
            ->add( 'coconutSystemPassword', PasswordType::class, [
                'label'                 => 'vs_vvp.form.video_platform_settings.coconut_system_password',
                'translation_domain'    => 'VanzVideoPlayer',
            ])
            
            ->add( 'coconutInputUrlType', ChoiceType::class, [
                'label'                 => 'vs_vvp.form.video_platform_settings.coconut_input_url_type',
                'translation_domain'    => 'VanzVideoPlayer',
                'choices'               => \array_flip( VideoPlatform::INPUT_URL_TYPES ),
                'expanded'              => true,
                'data'                  => empty( $entity->getCoconutInputUrlType() ) ?
                                            \array_key_first( VideoPlatform::INPUT_URL_TYPES ) :
                                            $entity->getCoconutInputUrlType(),
            ])
        ;
        
        $builder->get( 'coconutOutputFormats' )->addModelTransformer( new CoconutOutputFormatsTransformer() );
    }
    
    public function configureOptions( OptionsResolver $resolver ): void
    {
        parent::configureOptions( $resolver );
        
        $resolver
            ->setDefaults([
                'data_class'        => CoconutSettings::class,
                'csrf_protection'   => false,
            ])
        ;
    }
    
    public function getName()
    {
        return 'vs_vvp.coconut_settings';
    }
}