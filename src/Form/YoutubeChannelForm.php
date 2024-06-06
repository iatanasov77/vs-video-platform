<?php namespace App\Form;

use Vankosoft\ApplicationBundle\Form\AbstractForm;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use App\Entity\YoutubeChannel;
use App\Entity\GoogleCloudProject;

class YoutubeChannelForm extends AbstractForm
{
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        parent::buildForm( $builder, $options );
        
        $entity = $builder->getData();
        $builder
            ->add( 'title', TextType::class, [
                'label'                 => 'vs_vvp.form.youtube_channel.title',
                'translation_domain'    => 'VanzVideoPlayer',
            ])
            
            ->add( 'channelId', TextType::class, [
                'label'                 => 'vs_vvp.form.youtube_channel.channel_id',
                'translation_domain'    => 'VanzVideoPlayer',
            ])
            
            ->add( 'project', EntityType::class, [
                'label'                 => 'vs_vvp.form.youtube_channel.google_cloud_project',
                'translation_domain'    => 'VanzVideoPlayer',
                'class'                 => GoogleCloudProject::class,
                'choice_label'          => 'title',
                'placeholder'           => 'vs_vvp.form.youtube_channel.google_cloud_project_placeholder'
            ])
            
            ->add( 'photo', FileType::class, [
                'mapped'                => false,
                'required'              => ! $entity || is_null( $entity->getId() ),
                
                'label'                 => 'vs_vvp.form.youtube_channel.photo',
                'translation_domain'    => 'VanzVideoPlayer',
                
                'constraints'           => [
                    new File([
                        'maxSize'           => '1024k',
                        'mimeTypes'         => [
                            'image/gif',
                            'image/jpeg',
                            'image/png',
                            'image/svg+xml',
                        ],
                        'mimeTypesMessage'  => 'Please upload a valid Photo',
                    ])
                ],
            ])
        ;
    }
    
    public function configureOptions( OptionsResolver $resolver ): void
    {
        parent::configureOptions( $resolver );
        
        $resolver
            ->setDefaults([
                'data_class'        => YoutubeChannel::class,
                'csrf_protection'   => false,
            ])
        ;
    }
    
    public function getName()
    {
        return 'vs_vvp.youtube_channel';
    }
}