<?php namespace App\Controller\AdminPanel;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vankosoft\ApplicationBundle\Controller\AbstractCrudController;
use Vankosoft\ApplicationBundle\Controller\Traits\TaxonomyHelperTrait;

use App\Entity\VideoGenre;

class VideoGenreController extends AbstractCrudController
{
    use TaxonomyHelperTrait;
    
    protected function customData( Request $request, $entity = NULL ): array
    {
        $taxonomy   = $this->get( 'vs_application.repository.taxonomy' )->findByCode(
            $this->getParameter( 'vs_vvp.video-genres.taxonomy_code' )
        );
        
        $translations   = $this->classInfo['action'] == 'indexAction' ? $this->getTranslations() : [];
        if ( $entity && $entity->getTaxon() ) {
            $entity->getTaxon()->setCurrentLocale( $request->getLocale() );
        }
        
        return [
            'taxonomyId'    => $taxonomy->getId(),
            'translations'  => $translations,
            'items'         => $this->getRepository()->findAll(),
        ];
    }
    
    protected function prepareEntity( &$entity, &$form, Request $request )
    {
        $translatableLocale     = $form['currentLocale']->getData();
        $genreName           = $form['name']->getData();
        //$parentCategory         = $form['parent']->getData();
        
        /*
         * Create Category
         */
        if ( ! $translatableLocale ) {
            $translatableLocale = $request->getLocale();
        }
        $this->createVideoGenre( $entity, $genreName, $translatableLocale );
    }
    
    private function createVideoGenre(
        VideoGenre &$videoGenre,
        string $name,
        string $locale
    ): void {
        if ( $videoGenre->getTaxon() ) {
            $videoGenre->getTaxon()->setCurrentLocale( $locale );
            $videoGenre->getTaxon()->setName( $name );
            if ( $parentCategory ) {
                $videoGenre->getTaxon()->setParent( $parentCategory->getTaxon() );
            }
        } else {
            /*
             * @WORKAROUND Create Taxon If not exists
             */
            $taxonomy   = $this->get( 'vs_application.repository.taxonomy' )->findByCode(
                $this->getParameter( 'vs_vvp.video-genres.taxonomy_code' )
            );
            $newTaxon   = $this->createTaxon(
                $name,
                $locale,
                null,
                $taxonomy->getId()
            );
            
            $videoGenre->setTaxon( $newTaxon );
        }
    }
}
