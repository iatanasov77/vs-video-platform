<?php namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sylius\Component\Resource\Repository\RepositoryInterface;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use App\Component\ActorsFilter;

class ActorsFilterForm extends AbstractType
{
    /** @var string */
    private $videoGenreClass;
    
    /** @var RepositoryInterface */
    private $videoGenreRepository;
    
    /** @var ActorsFilter */
    private $actorsFilter;
    
    public function __construct(
        string $videoGenreClass,
        RepositoryInterface $videoGenreRepository,
        ActorsFilter $actorsFilter
    ) {
        $this->videoGenreClass          = $videoGenreClass;
        $this->videoGenreRepository     = $videoGenreRepository;
        $this->actorsFilter             = $actorsFilter;
    }
    
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder
            ->add( 'genre', EntityType::class, [
                'label'                 => 'vs_vvp.form.movies_filter.genre_label',
                'translation_domain'    => 'VanzVideoPlayer',
                'class'                 => $this->videoGenreClass,
                'choice_label'          => 'name',
                'placeholder'           => 'vs_vvp.form.movies_filter.genre_placeholder'
            ])
            
            ->add( 'rating', ChoiceType::class, [
                'label'                 => 'vs_vvp.form.movies_filter.rating_label',
                'translation_domain'    => 'VanzVideoPlayer',
                'choices'               => \array_flip( $this->actorsFilter->ratings() ),
                'placeholder'           => 'vs_vvp.form.movies_filter.rating_placeholder'
            ])
            
            ->add( 'order_by', ChoiceType::class, [
                'label'                 => 'vs_vvp.form.movies_filter.orderby_label',
                'translation_domain'    => 'VanzVideoPlayer',
                'choices'               => \array_flip( $this->actorsFilter->orderBy() ),
            ])
            
            ->add( 'btnSubmit', SubmitType::class, [
                'label'                 => 'vs_vvp.form.movies_filter.apply',
                'translation_domain'    => 'VanzVideoPlayer',
            ])
        ;
    }
    
    public function configureOptions( OptionsResolver $resolver ): void
    {
        $resolver
            ->setDefaults([
                'csrf_protection'   => false,
            ])
        ;
    }
}