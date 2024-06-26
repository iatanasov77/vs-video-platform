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
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

use App\Form\Type\ActorPhotoType;
use App\Entity\Actor;
use App\Component\VideoPlatform;

class ActorForm extends AbstractForm
{
    /** @var string */
    private $videoClass;
    
    /** @var string */
    private $genreClass;
    
    public function __construct(
        string $dataClass,
        RepositoryInterface $localesRepository,
        RequestStack $requestStack,
        string $videoClass,
        string $genreClass
    ) {
        parent::__construct( $dataClass );
        
        $this->localesRepository    = $localesRepository;
        $this->requestStack         = $requestStack;
        $this->videoClass           = $videoClass;
        $this->genreClass           = $genreClass;
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
            
            ->add( 'description', CKEditorType::class, [
                'label'                 => 'vs_vvp.form.actor.description',
                'translation_domain'    => 'VanzVideoPlayer',
                'config'                => $this->ckEditorConfig( $options ),
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
        
    }
    
    public function configureOptions( OptionsResolver $resolver ): void
    {
        parent::configureOptions( $resolver );
        
        $resolver
            ->setDefaults([
                'data_class'        => Actor::class,
                'csrf_protection'   => false,
                
                // CKEditor Options
                'ckeditor_uiColor'              => '#ffffff',
                'ckeditor_toolbar'              => 'full',
                'ckeditor_extraPlugins'         => '',
                'ckeditor_removeButtons'        => '',
                'ckeditor_allowedContent'       => false,
                'ckeditor_extraAllowedContent'  => '*[*]{*}(*)',
            ])
            
            ->setDefined([
                // CKEditor Options
                'ckeditor_uiColor',
                'ckeditor_toolbar',
                'ckeditor_extraPlugins',
                'ckeditor_removeButtons',
                'ckeditor_allowedContent',
                'ckeditor_extraAllowedContent',
            ])
            
            ->setAllowedTypes( 'ckeditor_uiColor', 'string' )
            ->setAllowedTypes( 'ckeditor_toolbar', 'string' )
            ->setAllowedTypes( 'ckeditor_extraPlugins', 'string' )
            ->setAllowedTypes( 'ckeditor_removeButtons', 'string' )
            ->setAllowedTypes( 'ckeditor_allowedContent', ['boolean', 'string'] )
            ->setAllowedTypes( 'ckeditor_extraAllowedContent', 'string' )
        ;
    }
    
    public function getName()
    {
        return 'vs_vvp.actor';
    }
    
    protected function ckEditorConfig( array $options ): array
    {
        $ckEditorConfig = [
            'uiColor'                           => $options['ckeditor_uiColor'],
            'toolbar'                           => $options['ckeditor_toolbar'],
            'extraPlugins'                      => array_map( 'trim', explode( ',', $options['ckeditor_extraPlugins'] ) ),
            'removeButtons'                     => $options['ckeditor_removeButtons'],
        ];
        
        $ckEditorAllowedContent = (bool)$options['ckeditor_allowedContent'];
        if ( $ckEditorAllowedContent ) {
            $ckEditorConfig['allowedContent']       = $ckEditorAllowedContent;
        } else {
            $ckEditorConfig['extraAllowedContent']  = $options['ckeditor_extraAllowedContent'];
        }
        
        return $ckEditorConfig;
    }
}