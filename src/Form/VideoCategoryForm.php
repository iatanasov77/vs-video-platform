<?php namespace App\Form;

use Vankosoft\ApplicationBundle\Form\AbstractForm;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

use App\Entity\VideoCategory;

class VideoCategoryForm extends AbstractForm
{
    /** @var string */
    private $categoryClass;
    
    /** @var TokenStorageInterface */
    private $tokenStorage;
    
    public function __construct(
        string $dataClass,
        RepositoryInterface $localesRepository,
        RequestStack $requestStack,
        TokenStorageInterface $tokenStorage
    ) {
        parent::__construct( $dataClass );
        
        $this->localesRepository    = $localesRepository;
        $this->requestStack         = $requestStack;
        
        $this->categoryClass        = $dataClass;
        $this->tokenStorage         = $tokenStorage;
    }
    
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        parent::buildForm( $builder, $options );
        
        $category   = $options['data'];
        
        $currentLocale  = $this->requestStack->getCurrentRequest()->getLocale();
        
        $builder
            ->add( '_currentUrl', HiddenType::class, ['mapped' => false] )
            
            ->add( 'currentLocale', ChoiceType::class, [
                'label'                 => 'vs_vvp.form.locale',
                'translation_domain'    => 'VanzVideoPlayer',
                'choices'               => \array_flip( $this->fillLocaleChoices() ),
                'data'                  => $currentLocale,
                'mapped'                => false,
            ])
            ->add( 'name', TextType::class, [
                'label'                 => 'vs_vvp.form.name',
                'translation_domain'    => 'VanzVideoPlayer',
            ])
            
            ->add( 'parent', EntityType::class, [
                'label'                 => 'vs_cms.form.category.parent_category',
                'translation_domain'    => 'VSCmsBundle',
                'class'                 => $this->categoryClass,
                'query_builder'         => function ( EntityRepository $er ) use ( $category )
                {
                    $qb = $er->createQueryBuilder( 'tc' );
                    if  ( $category && $category->getId() ) {
                        $qb->where( 'tc.id != :id' )->setParameter( 'id', $category->getId() );
                    }
                    
                    return $qb;
                },
                'choice_label'  => function ( $category ) {
                    return $category->getNameTranslated( $this->requestStack->getMainRequest()->getLocale() );
                },
                
                'required'      => false,
                'placeholder'   => 'vs_cms.form.category.parent_category_placeholder',
            ])
        ;
    }
    
    public function configureOptions( OptionsResolver $resolver ): void
    {
        parent::configureOptions( $resolver );
        
        $resolver->setDefaults([
            'data_class'        => VideoCategory::class,
            'csrf_protection'   => false,
        ]);
    }
    
    public function getName()
    {
        return 'vs_vvp.video_category';
    }
}
