<?php namespace App\Controller\AdminPanel;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vankosoft\ApplicationBundle\Controller\AbstractCrudController;

class VideoPlatformStorageController extends AbstractCrudController
{
    protected function customData( Request $request, $entity = NULL ): array
    {
        return [
            
        ];
    }
    
    protected function prepareEntity( &$entity, &$form, Request $request )
    {
        $formData    = $request->request->all( 'video_platform_storage_form' );
        //echo '<pre>'; var_dump( $formData ); die;
        
        $settings   = [];
        foreach ( $formData['settings'] as $settingsItem ) {
            if ( ! empty( $settingsItem['settingsKey'] ) && ! empty( $settingsItem['settingsValue'] ) ) {
                $settings[$settingsItem['settingsKey']] = $settingsItem['settingsValue'];
            }
        }
        
        $entity->setSettings( $settings );
    }
}