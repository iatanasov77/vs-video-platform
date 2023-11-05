<?php namespace App\Controller\AdminPanel;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vankosoft\ApplicationBundle\Controller\AbstractCrudController;
use Vankosoft\ApplicationBundle\Controller\TaxonomyHelperTrait;

use App\Entity\VideoCategory;

class VideoCategoryController extends AbstractCrudController
{
    use TaxonomyHelperTrait;
    
    protected function customData( Request $request, $entity = NULL ): array
    {
        $taxonomy   = $this->get( 'vs_application.repository.taxonomy' )->findByCode(
            $this->getParameter( 'vs_vvp.video-categories.taxonomy_code' )
        );
        
        $translations   = $this->classInfo['action'] == 'indexAction' ? $this->getTranslations() : [];
        if ( $entity && $entity->getTaxon() ) {
            $entity->getTaxon()->setCurrentLocale( $request->getLocale() );
        }
        
        return [
            'taxonomyId'    => $taxonomy->getId(),
            'translations'  => $translations,
        ];
    }
    
    protected function prepareEntity( &$entity, &$form, Request $request )
    {
        $translatableLocale     = $form['currentLocale']->getData();
        $categoryName           = $form['name']->getData();
        //$parentCategory         = $form['parent']->getData();
        $parentCategory         = $this->easyuiPost( $entity, $request->request->all( 'video_category_form' ) );
        
        /*
         * Create Category
         */
        if ( ! $translatableLocale ) {
            $translatableLocale = $request->getLocale();
        }
        $this->createVideoCategory( $entity, $categoryName, $translatableLocale, $parentCategory );
    }
    
    private function easyuiPost( VideoCategory &$entity, $formPost )
    {
        $repo       = $this->get( 'vs_vvp.repository.video_category' );
        if ( isset( $formPost['parent'] ) ) {
            //return $repo->findOneBy( ['taxon' => $formPost['parent']] );
            return $repo->find( $formPost['parent'] );
        }
        
        return null;
    }
    
    private function createVideoCategory(
        VideoCategory &$videoCategory,
        string $name,
        string $locale,
        ?VideoCategory $parentCategory
    ): void
    {
        if ( $videoCategory->getTaxon() ) {
            $videoCategory->getTaxon()->setCurrentLocale( $locale );
            $videoCategory->getTaxon()->setName( $name );
            if ( $parentCategory ) {
                $videoCategory->getTaxon()->setParent( $parentCategory->getTaxon() );
            }
            
            $videoCategory->setParent( $parentCategory );
        } else {
            /*
             * @WORKAROUND Create Taxon If not exists
             */
            $taxonomy   = $this->get( 'vs_application.repository.taxonomy' )->findByCode(
                $this->getParameter( 'vs_vvp.video-categories.taxonomy_code' )
            );
            $newTaxon   = $this->createTaxon(
                $name,
                $locale,
                $parentCategory ? $parentCategory->getTaxon() : null,
                $taxonomy->getId()
            );
            
            $videoCategory->setTaxon( $newTaxon );
            $videoCategory->setParent( $parentCategory );
        }
    }
}
