<?php namespace App\Form;

use Vankosoft\ApplicationBundle\Form\AbstractForm;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use daddl3\SymfonyCKEditor5WebpackViteBundle\Form\Ckeditor5TextareaType;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

use App\Form\Type\ActorPhotoType;
use App\Entity\Actor;
use App\Component\VideoPlatform;
use Vankosoft\CmsBundle\Form\Traits\FosCKEditor4Config;

class ActorForm extends AbstractForm
{
    use FosCKEditor4Config;
    
    /** @var string */
    private $videoClass;
    
    /** @var string */
    private $genreClass;
    
    /** @var string */
    private $useCkEditor;
    
    /** @var string */
    private $ckeditor5Editor;
    
    public function __construct(
        string $dataClass,
        RepositoryInterface $localesRepository,
        RequestStack $requestStack,
        string $videoClass,
        string $genreClass,
        
        string $useCkEditor,
        string $ckeditor5Editor
    ) {
        parent::__construct( $dataClass );
        
        $this->localesRepository    = $localesRepository;
        $this->requestStack         = $requestStack;
        $this->videoClass           = $videoClass;
        $this->genreClass           = $genreClass;
        
        $this->useCkEditor          = $useCkEditor;
        $this->ckeditor5Editor      = $ckeditor5Editor;
    }
    
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        parent::buildForm( $builder, $options );
        
        $entity         = $builder->getData();
        $currentLocale  = $entity->getTranslatableLocale() ?: $this->requestStack->getCurrentRequest()->getLocale();
        
        $builder
            ->add( 'actorVideos', HiddenType::class, [
                'mapped'    => false,
                'data'      => \json_encode( $entity->getVideos()->getKeys() )
            ])
            
            ->add( 'actorGenres', HiddenType::class, [
                'mapped'    => false,
                'data'      => \json_encode( $entity->getGenres()->getKeys() )
            ])
        
            ->add( 'locale', ChoiceType::class, [
                'label'                 => 'vs_vvp.form.locale',
                'translation_domain'    => 'VanzVideoPlayer',
                'choices'               => \array_flip( $this->fillLocaleChoices() ),
                'data'                  => $currentLocale,
                'mapped'                => false,
            ])
            
            ->add( 'name', TextType::class, [
                'label'                 => 'vs_vvp.form.actor.name',
                'translation_domain'    => 'VanzVideoPlayer',
            ])
            
            ->add( 'photos', CollectionType::class, [
                'entry_type'   => ActorPhotoType::class,
                'allow_add'    => true,
                'allow_delete' => true,
                'prototype'    => true,
                'by_reference' => false
            ])
            
            ->add( 'videos', EntityType::class, [
                'label'                 => 'vs_vvp.form.actor.videos',
                'translation_domain'    => 'VanzVideoPlayer',
                'multiple'              => true,    // Multiple Can be Changed in Template
                'required'              => false,
                //'mapped'                => false,
                'placeholder'           => 'vs_vvp.form.actor.videos_placeholder',
                
                'class'                 => $this->videoClass,
                'choice_label'          => 'title'
            ])
            
            ->add( 'career', ChoiceType::class, [
                'label'                 => 'vs_vvp.form.actor.career',
                'translation_domain'    => 'VanzVideoPlayer',
                'choices'               => \array_flip( VideoPlatform::VIDEO_ACTOR_CAREERS ),
                'required'              => false,
            ])
            
            ->add( 'height', NumberType::class, [
                'label'                 => 'vs_vvp.form.actor.height',
                'translation_domain'    => 'VanzVideoPlayer',
                'required'              => false,
                
                //'html5'                 => true,
                'scale'                 => 2,
            ])
            
            ->add( 'dateOfBirth', DateType::class, [
                'label'                 => 'vs_vvp.form.actor.date_of_birth',
                'translation_domain'    => 'VanzVideoPlayer',
                'required'              => false,
                
                'widget'                => 'single_text',
                'input_format'          => 'd.M.Y',
            ])
            
            ->add( 'placeOfBirth', TextType::class, [
                'label'                 => 'vs_vvp.form.actor.place_of_birth',
                'translation_domain'    => 'VanzVideoPlayer',
                'required'              => false,
            ])
            
            ->add( 'genres', EntityType::class, [
                'label'                 => 'vs_vvp.form.actor.genres',
                'translation_domain'    => 'VanzVideoPlayer',
                'multiple'              => true,    // Multiple Can be Changed in Template
                'required'              => false,
                //'mapped'                => false,
                'placeholder'           => 'vs_vvp.form.actor.genres_placeholder',
                
                'class'                 => $this->genreClass,
                'choice_label'          => 'name'
            ])
        ;
        
        if ( $this->useCkEditor == '5' ) {
            $builder->add( 'description', Ckeditor5TextareaType::class, [
                'label'                 => 'vs_vvp.form.actor.description',
                'translation_domain'    => 'VanzVideoPlayer',
                'attr' => [
                    'data-ckeditor5-config' => 'devpage'
                ],
            ]);
        } else {
            $builder->add( 'description', CKEditorType::class, [
                'label'                 => 'vs_vvp.form.actor.description',
                'translation_domain'    => 'VanzVideoPlayer',
                'config'                => $this->ckEditorConfig( $options ),
            ]);
        }
    }
    
    public function configureOptions( OptionsResolver $resolver ): void
    {
        parent::configureOptions( $resolver );
        
        $resolver
            ->setDefaults([
                'data_class'        => Actor::class,
                'csrf_protection'   => false,
            ])
        ;
            
        $this->onfigureCkEditorOptions( $resolver );
    }
    
    public function getName()
    {
        return 'vs_vvp.actor';
    }
}