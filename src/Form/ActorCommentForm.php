<?php namespace App\Form;

use Vankosoft\CatalogBundle\Form\AbstractCommentForm;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ActorCommentForm extends AbstractCommentForm
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
        return 'vs_vvp_actor_comment';
    }
}