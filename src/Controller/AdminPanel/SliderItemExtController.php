<?php namespace App\Controller\AdminPanel;

use Vankosoft\CmsBundle\Controller\SliderItemExtController as BaseSliderItemExtController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Form\SliderItemForm;

class SliderItemExtController extends BaseSliderItemExtController
{
    public function editSliderItem( $sliderId, $itemId, $locale, Request $request ): Response
    {
        $slider = $this->sliderRepository->find( $sliderId );
        $em     = $this->doctrine->getManager();
        
        $itemId         = intval( $itemId );
        $sliderItem     = $itemId ? $this->sliderItemRepository->find( $itemId ) : $this->sliderItemFactory->createNew();
        $formAction     = $itemId ?
        $this->generateUrl( 'vs_cms_slider_item_update', ['sliderId' => $sliderId, 'id' => $itemId] ) :
        $this->generateUrl( 'vs_cms_slider_item_create', ['sliderId' => $sliderId] );
        $formMethod     = $itemId ? 'PUT' : 'POST';
        
        if ( $locale != $request->getLocale() ) {
            $sliderItem->setTranslatableLocale( $locale );
            $em->refresh( $sliderItem );
        }
        
        $form   = $this->createForm( SliderItemForm::class, $sliderItem, [
            'action'                        => $formAction,
            'method'                        => $formMethod,
            'data'                          => $sliderItem,
            'slider'                        => $slider,
            
            'ckeditor_uiColor'              => $this->getParameter( 'vs_cms.form.decription_field.ckeditor_uiColor' ),
            'ckeditor_toolbar'              => $this->getParameter( 'vs_cms.form.decription_field.ckeditor_toolbar' ),
            'ckeditor_extraPlugins'         => $this->getParameter( 'vs_cms.form.decription_field.ckeditor_extraPlugins' ),
            'ckeditor_removeButtons'        => $this->getParameter( 'vs_cms.form.decription_field.ckeditor_removeButtons' ),
            'ckeditor_allowedContent'       => $this->getParameter( 'vs_cms.form.decription_field.ckeditor_allowedContent' ),
            'ckeditor_extraAllowedContent'  => $this->getParameter( 'vs_cms.form.decription_field.ckeditor_extraAllowedContent' ),
        ]);
        
        return $this->render( '@VSCms/Pages/SlidersItems/slider_item_form.html.twig', [
            'form'                      => $form->createView(),
            'sliderId'                  => $sliderId,
            'item'                      => $sliderItem,
            'sliderPhotoDescription'    => $this->sliderPhotoDescription,
            'isAjaxRequest'             => true,
        ]);
    }
}