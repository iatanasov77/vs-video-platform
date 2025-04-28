<?php namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class ActorPhotoType extends AbstractType
{
    /** @var string */
    protected $dataClass;
    
    public function __construct( string $dataClass )
    {
        $this->dataClass    = $dataClass;
    }
    
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        $entity = $builder->getData();
        //var_dump( $entity ); die;
        
        $builder
            ->add( 'id', HiddenType::class, ['mapped' => false] )
            
            ->add( 'photo', FileType::class, [
                'mapped'                => false,
                'required'              => ! $entity || is_null( $entity->getId() ),
                
                'label'                 => 'vs_vvp.form.actor.photo',
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
        $resolver->setDefaults([
            'data_class' => $this->dataClass
        ]);
    }
    
    public function getName()
    {
        return 'ActorPhotoField';
    }
}