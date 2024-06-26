<?php namespace App\Form;

use Vankosoft\ApplicationBundle\Form\AbstractForm;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

use App\Form\Type\VideoPhotoType;
use App\Entity\Video;
use App\Entity\VideoCategory;

class VideoForm extends AbstractForm
{
    /** @var TokenStorageInterface */
    private $tokenStorage;
    
    /** @var string */
    private $videoCategoryClass;
    
    /** @var string */
    private $videoGenreClass;
    
    /** @var string */
    private $actorClass;
    
    /** @var string */
    private $paidServiceClass;
    
    public function __construct(
        string $dataClass,
        RepositoryInterface $localesRepository,
        RequestStack $requestStack,
        TokenStorageInterface $tokenStorage,
        string $videoCategoryClass,
        string $videoGenreClass,
        string $actorClass,
        string $paidServiceClass
    ) {
        parent::__construct( $dataClass );
        
        $this->localesRepository    = $localesRepository;
        $this->requestStack         = $requestStack;
        
        $this->tokenStorage         = $tokenStorage;
        
        $this->videoCategoryClass   = $videoCategoryClass;
        $this->videoGenreClass      = $videoGenreClass;
        $this->actorClass           = $actorClass;
        $this->paidServiceClass     = $paidServiceClass;
    }
    
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        parent::buildForm( $builder, $options );
        
        $entity         = $builder->getData();
        $currentLocale  = $entity->getTranslatableLocale() ?: $this->requestStack->getCurrentRequest()->getLocale();
        
        if ( $entity->getId() ) {
            $requiredResources  = [];
        } else {
            $requiredResources  = ["VsVvp_VideoFile"];
        }
        
        $builder
            ->add( 'locale', ChoiceType::class, [
                'label'                 => 'vs_vvp.form.locale',
                'translation_domain'    => 'VanzVideoPlayer',
                'choices'               => \array_flip( $this->fillLocaleChoices() ),
                'data'                  => $currentLocale,
                'mapped'                => false,
            ])
        
            ->add( 'id', HiddenType::class, [
                'mapped'    => false,
                'data'      => $entity->getId()
            ])
            
            ->add( 'videoFileId', HiddenType::class, [
                'mapped'    => false,
                'data'      => $entity->getId() ? $entity->getVideoFile()->getId() : 0
            ])
            
            ->add( 'videoCategories', HiddenType::class, [
                'mapped'    => false,
                'data'      => \json_encode( $entity->getCategories()->getKeys() )
            ])
            
            ->add( 'videoGenres', HiddenType::class, [
                'mapped'    => false,
                'data'      => \json_encode( $entity->getGenres()->getKeys() )
            ])
            
            ->add( 'videoActors', HiddenType::class, [
                'mapped'    => false,
                'data'      => \json_encode( $entity->getActors()->getKeys() )
            ])
            
            ->add( 'videoAllowedPaidServices', HiddenType::class, [
                'mapped'    => false,
                'data'      => \json_encode( $entity->getAllowedPaidServices()->getKeys() )
            ])
            
            ->add( 'tagsInputWhitelist', HiddenType::class, ['mapped' => false] )
            ->add( 'requiredResources', HiddenType::class, [
                'mapped'    => false,
                'data'      => \json_encode( $requiredResources )
            ])
            
            ->add( 'enabled', CheckboxType::class, [
                'required'              => false,
                
                'label'                 => 'vs_vvp.form.public',
                'translation_domain'    => 'VanzVideoPlayer',
            ])
            
            ->add( 'name', TextType::class, [
                'label'                 => 'vs_vvp.form.video.title',
                'translation_domain'    => 'VanzVideoPlayer',
            ])
            
            ->add( 'tags', TextType::class, [
                'label'                 => 'vs_vvp.form.video.tags',
                'translation_domain'    => 'VanzVideoPlayer',
            ])
            
            ->add( 'video', FileType::class, [
                'mapped' => false,
                'required' => is_null( $entity->getId() ),
                
                'label'                 => 'vs_vvp.form.video.video',
                'translation_domain'    => 'VanzVideoPlayer',
                
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            //'application/octet-stream',
                            'video/mp4',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid Video file',
                    ])
                ],
            ])
            
            ->add( 'category_taxon', EntityType::class, [
                'label'                 => 'vs_cms.form.page.categories',
                'translation_domain'    => 'VSCmsBundle',
                'multiple'              => true,    // Multiple Can be Changed in Template
                'required'              => false,
                
                // Pass Data When 'mapped' is false
                'mapped'                => false,
                'data'                  => $entity->getCategories(),
                
                'placeholder'           => 'vs_cms.form.page.categories_placeholder',
                
                'class'                 => $this->videoCategoryClass,
                'choice_label'          => function ( VideoCategory $category ) {
                    return $category->getNameTranslated( $this->requestStack->getMainRequest()->getLocale() );
                },
                'choice_value'          => function ( VideoCategory $category ) {
                    //return $category ? $category->getTaxon()->getId() : 0;
                    return $category ? $category->getId() : 0;
                },
            ])
            
            ->add( 'genres', EntityType::class, [
                'label'                 => 'vs_vvp.form.actor.genres',
                'translation_domain'    => 'VanzVideoPlayer',
                'multiple'              => true,    // Multiple Can be Changed in Template
                'required'              => false,
                //'mapped'                => false,
                'placeholder'           => 'vs_vvp.form.actor.genres_placeholder',
                
                'class'                 => $this->videoGenreClass,
                'choice_label'          => 'name'
            ])
            
            ->add( 'description', CKEditorType::class, [
                'label'                 => 'vs_vvp.form.video.description',
                'translation_domain'    => 'VanzVideoPlayer',
                'config'                => $this->ckEditorConfig( $options ),
            ])
            
            ->add( 'photos', CollectionType::class, [
                'entry_type'   => VideoPhotoType::class,
                'allow_add'    => true,
                'allow_delete' => true,
                'prototype'    => true,
                'by_reference' => false
            ])
            
            ->add( 'actors', EntityType::class, [
                'label'                 => 'vs_vvp.form.video.actors',
                'translation_domain'    => 'VanzVideoPlayer',
                'multiple'              => true,    // Multiple Can be Changed in Template
                'required'              => false,
                //'mapped'                => false,
                'placeholder'           => 'vs_vvp.form.video.actors_placeholder',
                
                'class'                 => $this->actorClass,
                'choice_label'          => 'name'
            ])
            
            ->add( 'allowedPaidServices', EntityType::class, [
                'label'                 => 'vs_vvp.form.video.allowed_paid_services',
                'translation_domain'    => 'VanzVideoPlayer',
                'multiple'              => true,    // Multiple Can be Changed in Template
                'required'              => false,
                //'mapped'                => false,
                'placeholder'           => 'vs_vvp.form.video.allowed_paid_services_placeholder',
                
                'class'                 => $this->paidServiceClass,
                'choice_label'          => 'title'
            ])
        ;
    }
    
    public function configureOptions( OptionsResolver $resolver ): void
    {
        parent::configureOptions( $resolver );
        
        $resolver
            ->setDefaults([
                'data_class'        => Video::class,
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
        return 'vs_vvp.video';
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
