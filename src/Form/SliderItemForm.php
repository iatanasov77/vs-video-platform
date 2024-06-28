<?php namespace App\Form;

use Vankosoft\CmsBundle\Form\SliderItemForm as BaseSliderItemForm;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Actor;
use App\Entity\Video;

class SliderItemForm extends BaseSliderItemForm
{
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        parent::buildForm( $builder, $options );
        
        $builder
            ->add( 'actor', EntityType::class, [
                'required'              => false,
                'label'                 => 'vs_cms.form.slider_item.actor',
                'translation_domain'    => 'VSCmsBundle',
                'class'                 => Actor::class,
                'choice_label'          => 'name',
                'placeholder'           => 'vs_cms.form.slider_item.actor_placeholder',
            ])
            
            ->add( 'video', EntityType::class, [
                'required'              => false,
                'label'                 => 'vs_cms.form.slider_item.video',
                'translation_domain'    => 'VSCmsBundle',
                'class'                 => Video::class,
                'choice_label'          => 'name',
                'placeholder'           => 'vs_cms.form.slider_item.video_placeholder',
            ])
        ;
    }
}