<?php namespace App\Form;

use Vankosoft\ApplicationBundle\Form\AbstractForm;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;

use App\Entity\GoogleCloudProject;

class GoogleCloudProjectForm extends AbstractForm
{
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        parent::buildForm( $builder, $options );
        
        $builder
            ->add( 'title', TextType::class, [
                'label'                 => 'vs_vvp.form.google_cloud_project.title',
                'translation_domain'    => 'VanzVideoPlayer',
            ])
            
            ->add( 'googleApiKey', TextType::class, [
                'label'                 => 'vs_vvp.form.google_cloud_project.api_key',
                'translation_domain'    => 'VanzVideoPlayer',
            ])
            
            ->add( 'googleClientId', TextType::class, [
                'label'                 => 'vs_vvp.form.google_cloud_project.client_id',
                'translation_domain'    => 'VanzVideoPlayer',
            ])
            
            ->add( 'googleClientSecret', TextType::class, [
                'label'                 => 'vs_vvp.form.google_cloud_project.client_secret',
                'translation_domain'    => 'VanzVideoPlayer',
            ])
        ;
        
    }
    
    public function configureOptions( OptionsResolver $resolver ): void
    {
        parent::configureOptions( $resolver );
        
        $resolver
            ->setDefaults([
                'data_class'        => GoogleCloudProject::class,
                'csrf_protection'   => false,
            ])
        ;
    }
    
    public function getName()
    {
        return 'vs_vvp.google_cloud_project';
    }
}