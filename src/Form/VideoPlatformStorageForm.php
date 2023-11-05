<?php namespace App\Form;

use Vankosoft\ApplicationBundle\Form\AbstractForm;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use App\Form\DataTransformer\VideoPlatformStorageSettingsTransformer;
use App\Form\Type\VideoPlatformStorageSettingsType;

use App\Entity\VideoPlatformStorage;
use App\Component\VideoPlatform;

class VideoPlatformStorageForm extends AbstractForm
{
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        parent::buildForm( $builder, $options );
        
        $entity = $builder->getData();
        //var_dump( $entity->getSettings() ); die;
        
        $builder
            ->add( 'title', TextType::class, [
                'label'                 => 'vs_vvp.form.video_platform_storage.title',
                'translation_domain'    => 'VanzVideoPlayer',
            ])
            
            ->add( 'storageType', ChoiceType::class, [
                'label'                 => 'vs_vvp.form.video_platform_storage.storage_type',
                'translation_domain'    => 'VanzVideoPlayer',
                'choices'               => \array_flip( VideoPlatform::STORAGE_TYPES ),
                'placeholder'           => 'vs_vvp.form.video_platform_storage.storage_type_placeholder'
            ])
            
            ->add( 'settings', CollectionType::class, [
                'entry_type'   => VideoPlatformStorageSettingsType::class,
                'allow_add'    => true,
                'allow_delete' => true,
                'prototype'    => true,
                'by_reference' => false,
                
                'entry_options' => [
                    'data' => $entity->getSettings()
                ],
            ])
        ;
            
        $builder->get( 'settings' )->addModelTransformer( new VideoPlatformStorageSettingsTransformer() );
    }
    
    public function configureOptions( OptionsResolver $resolver ): void
    {
        parent::configureOptions( $resolver );
        
        $resolver
        ->setDefaults([
            'data_class'        => VideoPlatformStorage::class,
            'csrf_protection'   => false,
        ])
        ;
    }
    
    public function getName()
    {
        return 'vs_vvp.video_platform_storage';
    }
}