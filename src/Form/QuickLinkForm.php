<?php namespace App\Form;

use Vankosoft\ApplicationBundle\Form\AbstractForm;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use App\Entity\QuickLink;

class QuickLinkForm extends AbstractForm
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
        
        $entity         = $builder->getData();
        $currentLocale  = $entity->getTranslatableLocale() ?: $this->requestStack->getCurrentRequest()->getLocale();
        
        $builder
            ->add( 'locale', ChoiceType::class, [
                'label'                 => 'vs_vvp.form.locale',
                'translation_domain'    => 'VanzVideoPlayer',
                'choices'               => \array_flip( $this->fillLocaleChoices() ),
                'data'                  => $currentLocale,
                'mapped'                => false,
            ])
            
            ->add( 'linkText', TextType::class, [
                'label'                 => 'vs_vvp.form.quick_link.link_text',
                'translation_domain'    => 'VanzVideoPlayer',
            ])
            
            ->add( 'linkPath', TextType::class, [
                'label'                 => 'vs_vvp.form.quick_link.link_path',
                'translation_domain'    => 'VanzVideoPlayer',
            ])
        ;
        
    }
    
    public function configureOptions( OptionsResolver $resolver ): void
    {
        parent::configureOptions( $resolver );
        
        $resolver->setDefaults([
            'data_class'        => QuickLink::class,
            'csrf_protection'   => false,
        ]);
    }
    
    public function getName()
    {
        return 'vs_vvp.quick_link';
    }
}