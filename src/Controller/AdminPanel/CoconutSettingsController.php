<?php namespace App\Controller\AdminPanel;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vankosoft\ApplicationBundle\Controller\AbstractCrudController;

class CoconutSettingsController extends AbstractCrudController
{
    protected function customData( Request $request, $entity = NULL ): array
    {
        return [
            
        ];
    }
    
    protected function prepareEntity( &$entity, &$form, Request $request )
    {
        $formData    = $request->request->all( 'coconut_settings_form' );
        //echo '<pre>'; var_dump( $formData ); die;
        
        $coconutOutputFormats   = [];
        foreach ( $formData['coconutOutputFormats'] as $format ) {
            if ( empty( $format['format'] ) ) {
                continue;
            }
            $coconutOutputFormats[] = $format['format'];
        }
        
        $entity->setCoconutOutputFormats( $coconutOutputFormats );
    }
}