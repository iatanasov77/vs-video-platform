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

use App\Entity\VideoGenre;

class VideoGenreForm extends AbstractForm
{ 
    public function __construct(
        string $dataClass,
        RepositoryInterface $localesRepository,
        RequestStack $requestStack
    ) {
        parent::__construct( $dataClass );
        
        $this->localesRepository    = $localesRepository;
        $this->requestStack         = $requestStack;
    }
    
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        parent::buildForm( $builder, $options );
        
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
        ;
    }
    
    public function configureOptions( OptionsResolver $resolver ): void
    {
        parent::configureOptions( $resolver );
        
        $resolver->setDefaults([
            'data_class'        => VideoGenre::class,
            'csrf_protection'   => false,
        ]);
    }
    
    public function getName()
    {
        return 'vs_vvp.video_category';
    }
}
