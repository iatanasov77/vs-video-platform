<?php namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

use App\Component\VideoPlatform;

class VideoPhotoType extends AbstractType
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
            
            ->add( 'code', ChoiceType::class, [
                'label'                 => 'vs_vvp.form.video.photo_code',
                'placeholder'           => 'vs_vvp.form.video.photo_code_placeholder',
                'translation_domain'    => 'VanzVideoPlayer',
                'choices'               => \array_flip( VideoPlatform::VIDEO_PHOTO_TYPES ),
                //'required'              => ! $entity || is_null( $entity->getId() ),
                'required'              => false,
            ])
            
            ->add( 'description', TextType::class, [
                'label'                 => 'vs_vvp.form.video.photo_description',
                'translation_domain'    => 'VanzVideoPlayer',
                'required'              => false,
            ])
            
            ->add( 'photo', FileType::class, [
                'mapped'                => false,
                //'required'              => ! $entity || is_null( $entity->getId() ),
                'required'              => false,
                
                'label'                 => 'vs_vvp.form.video.photo',
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
        return 'VideoPhotoField';
    }
}