<?php namespace App\Controller\AdminPanel;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

trait FilterFormTrait
{
    protected function getFilterForm( $filterClass, $selected, $request )
    {
        $filterForm     = $this->createFormBuilder()
                            ->add( 'filterByCategory', EntityType::class, [
                                'class'                 => $filterClass,
                                'choice_label'          => function ( $category ) use ( $request )
                                {
                                    return $category->getNameTranslated( $request->getLocale() );
                                },
                                'required'              => true,
                                'label'                 => 'vs_vvp.form.videos_filter.filter_by_category',
                                'placeholder'           => 'vs_vvp.form.videos_filter.category_placeholder',
                                'translation_domain'    => 'VanzVideoPlayer',
                                'data'                  => $selected ?
                                                            $this->getFilterRepository()->find( $selected ) :
                                                            null,
                            ])
                            ->getForm();
                            
        return $filterForm;
    }
    
    abstract protected function getFilterRepository();
}
