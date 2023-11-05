<?php namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use App\Component\MoviesFilter;

class MoviesFilterForm extends AbstractType
{
    /** @var string */
    private $videoCategoryClass;
    
    /** @var RepositoryInterface */
    private $videoCategoryRepository;
    
    /** @var MoviesFilter */
    private $moviesFilter;
    
    public function __construct(
        string $videoCategoryClass,
        RepositoryInterface $videoCategoryRepository,
        MoviesFilter $moviesFilter
    ) {
        $this->videoCategoryClass       = $videoCategoryClass;
        $this->videoCategoryRepository  = $videoCategoryRepository;
        $this->moviesFilter             = $moviesFilter;
    }
    
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder
            ->add( 'category', EntityType::class, [
                'label'                 => 'vs_vvp.form.movies_filter.category_label',
                'translation_domain'    => 'VanzVideoPlayer',
                'class'                 => $this->videoCategoryClass,
                'choice_label'          => 'name',
                'placeholder'           => 'vs_vvp.form.movies_filter.category_placeholder'
            ])
            
            ->add( 'quality', ChoiceType::class, [
                'label'                 => 'vs_vvp.form.movies_filter.quality_label',
                'translation_domain'    => 'VanzVideoPlayer',
                'choices'               => \array_flip( $this->moviesFilter->qualities() ),
                'placeholder'           => 'vs_vvp.form.movies_filter.quality_placeholder'
            ])
            
            ->add( 'rating', ChoiceType::class, [
                'label'                 => 'vs_vvp.form.movies_filter.rating_label',
                'translation_domain'    => 'VanzVideoPlayer',
                'choices'               => \array_flip( $this->moviesFilter->ratings() ),
                'placeholder'           => 'vs_vvp.form.movies_filter.rating_placeholder'
            ])
            
            ->add( 'order_by', ChoiceType::class, [
                'label'                 => 'vs_vvp.form.movies_filter.orderby_label',
                'translation_domain'    => 'VanzVideoPlayer',
                'choices'               => \array_flip( $this->moviesFilter->orderBy() ),
            ])
            
            ->add( 'btnSubmit', SubmitType::class, [
                'label'                 => 'vs_vvp.form.movies_filter.apply',
                'translation_domain'    => 'VanzVideoPlayer',
            ])
        ;
    }
    
    
}