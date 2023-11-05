<?php namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;

class CoconutOutputFormatType extends AbstractType
{
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        //echo '<pre>'; var_dump( $builder->getData() ); die;
        $builder
            ->add( 'format', TextType::class, [
                'label'                 => 'vs_vvp.form.video_platform_settings.format',
                'attr'                  => [
                    'placeholder'   => 'mp4:1080p'
                ],
                'translation_domain'    => 'VanzVideoPlayer',
                'required'              => false,
            ])
        ;
    }
    
    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setDefaults([
            'csrf_protection'       => false,
        ]);
    }
    
    public function getName()
    {
        return 'CoconutOutputFormatField';
    }
}