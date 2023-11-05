<?php namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class CoconutOutputFormatsTransformer implements DataTransformerInterface
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
        foreach ( $value as $val ) {
            $transformed[]  = [
                'format'    => $val,
            ];
        }
        
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
            $reversed[] = $val['format'];
        }
        
        return $reversed;
    }
}
