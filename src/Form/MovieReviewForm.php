<?php namespace App\Form;

use Vankosoft\CatalogBundle\Form\AbstractReviewForm;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class MovieReviewForm extends AbstractReviewForm
{
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        parent::buildForm( $builder, $options );
    }
    
    public function configureOptions( OptionsResolver $resolver ): void
    {
        parent::configureOptions( $resolver );
        
        $resolver->setDefaults([
            'csrf_protection'   => false,
        ]);
    }
    
    public function getBlockPrefix(): string
    {
        return 'vs_vvp_movie_review';
    }
}