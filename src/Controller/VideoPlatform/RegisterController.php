<?php namespace App\Controller\VideoPlatform;

use Vankosoft\UsersBundle\Controller\RegisterController as BaseRegisterController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Resource\Factory\Factory;
use Vankosoft\ApplicationBundle\Component\Context\ApplicationContextInterface;
use Vankosoft\UsersBundle\Security\UserManager;
use Vankosoft\UsersBundle\Security\AnotherLoginFormAuthenticator;
use Symfony\Component\IntlSubdivision\IntlSubdivision;
use Vankosoft\CatalogBundle\EventSubscriber\Event\CreateNewUserSubscriptionEvent;

class RegisterController extends BaseRegisterController
{
    use GlobalFormsTrait;
    
    /** @var RepositoryInterface */
    private $pricingPlanRepository;
    
    /** @var EventDispatcherInterface */
    private $eventDispatcher;
    
    public function __construct(
        ManagerRegistry $doctrine,
        TranslatorInterface $translator,
		ApplicationContextInterface $applicationContext,
        UserManager $userManager,
        RepositoryInterface $usersRepository,
        Factory $usersFactory,
        RepositoryInterface $userRolesRepository,
        MailerInterface $mailer,
        RepositoryInterface $pagesRepository,
        UserAuthenticatorInterface $guardHandler,
        AnotherLoginFormAuthenticator $authenticator,
        array $parameters,
        RepositoryInterface $pricingPlanRepository,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct(
            $doctrine,
            $translator,
            $applicationContext,
            $userManager,
            $usersRepository,
            $usersFactory,
            $userRolesRepository,
            $mailer,
            $pagesRepository,
            $guardHandler,
            $authenticator,
            $parameters
        );
        
        $this->pricingPlanRepository    = $pricingPlanRepository;
        $this->eventDispatcher          = $eventDispatcher;
    }
    
    public function index( Request $request, MailerInterface $mailer ): Response
    {
        if ( $this->getUser() ) {
            return $this->redirectToRoute( $this->params['defaultRedirect'] );
        }
        
        if ( $request->isMethod( 'post' ) ) {
            //return parent::index( $request, $mailer );
            return $this->handleRegisterForm( $request, $mailer );
        }
        
        $params = [
            //'shoppingCart'  => $this->getShoppingCart( $request ),
        ];

        return $this->render( '@VSUsers/Register/register.html.twig', array_merge( $params, $this->templateParams( $this->getForm() ) ) );
    }
    
    /*
    public function getStatesForCountry( $countryCode, Request $request ): Response
    {
        $states = IntlSubdivision::getStatesAndProvincesForCountry( $countryCode );
        
        return new JsonResponse( $states );
    }
    */
    
    public function afterVerifyAction( Request $request ): Response
    {
        $user   = $this->getUser();
        
        if ( ! $user ) {
            return $this->redirectToRoute( $this->params['defaultRedirect'] );
        }
        
        $subscriptions  = $user->getPricingPlanSubscriptions();
        if ( ! $subscriptions->isEmpty() ) {
            $pricingPlanId  = $subscriptions->first()->getPricingPlan()->getId();
            return $this->redirectToRoute( 'vs_payment_select_payment_method_form', ['pricingPlanId' => $pricingPlanId] );
        }
        
        return $this->redirectToRoute( $this->params['defaultRedirect'] );
    }
    
    protected function handleRegisterForm( Request $request, MailerInterface $mailer )
    {
        $form   = $this->getForm();
        $form->handleRequest( $request );
        if ( $form->isSubmitted() ) {
            $em             = $this->doctrine->getManager();
            $formUser       = $form->getData();
            $plainPassword  = $form->get( "plain_password" )->getData();
            $oUser          = $this->userManager->createUser(
                \strstr( $formUser->getEmail(), '@', true ),
                $formUser->getEmail(),
                $plainPassword
            );
            
            /** Prepare User */
            $this->prepareUser( $oUser, $form );
            
            /** Populate UserInfo Values */
            $this->populateUserInfo( $oUser, $form );
            
            $em->persist( $oUser );
            $em->flush();
            
            $pricingPlanId  = $form->get( "pricingPlan" )->getData();
            if ( $pricingPlanId ) {
                $pricingPlan    = $this->pricingPlanRepository->find( $pricingPlanId );
                $this->eventDispatcher->dispatch(
                    new CreateNewUserSubscriptionEvent( $oUser, $pricingPlan ),
                    CreateNewUserSubscriptionEvent::NAME
                );
            }
            
            $this->sendConfirmationMail( $oUser, $mailer );
            
            $this->addFlash(
                'success',
                $this->translator->trans( 'vs_application.form.register.alert_success', [], 'VSApplicationBundle' )
            );
            
            return $this->redirectToRoute( $this->params['defaultRedirect'] );
        }
    }
    
    protected function prepareUser( &$oUser, $form )
    {
        $oUser->addRole( $this->userRolesRepository->findByTaxonCode( $this->params['registerRole'] ) );
        $oUser->addApplication( $this->applicationContext->getApplication() );
        
        $preferedLocale = $form->get( "prefered_locale" )->getData();
        $oUser->setPreferedLocale( $preferedLocale );
        $oUser->setVerified( false );
        $oUser->setEnabled( false );
    }
    
    protected function populateUserInfo( &$oUser, $form )
    {
        $oUser->getInfo()->setTitle( $form->get( "title" )->getData() );
        $oUser->getInfo()->setFirstName( $form->get( "firstName" )->getData() );
        $oUser->getInfo()->setLastName( $form->get( "lastName" )->getData() );
        $oUser->getInfo()->setBirthday( $form->get( "birthday" )->getData() );
    }
}
