<?php namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class VideoPlatformStorageSettingsTransformer implements DataTransformerInterface
{
    
    /**
     * @inheritDoc
     */
    public function transform( $value ): array
    {
        //echo '<pre>'; var_dump( $value ); die;
        if ( empty( $value ) ) {
            return [];
        }
        
        $transformed    = [];
        foreach ( $value as $key => $val ) {
            $transformed[]  = [
                'settingsKey'   => $key,
                'settingsValue' => $val,
            ];
        }
        
        //echo '<pre>'; var_dump( $transformed ); die;
        return $transformed;
    }
    
    /**
     * @ihneritdoc
     */
    public function reverseTransform( $value ): array
    {
        //echo '<pre>'; var_dump( $value ); die;
        if ( empty( $value ) ) {
            return [];
        }
        
        $reversed    = [];
        foreach ( $value as $val ) {
            $reversed[$val['settingsKey']] = $val['settingsValue'];
        }
        
        return $reversed;
    }
}
