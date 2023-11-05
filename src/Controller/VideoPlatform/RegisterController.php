<?php namespace App\Controller\VideoPlatform;

use Vankosoft\UsersBundle\Controller\RegisterController as BaseRegisterController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Resource\Factory\Factory;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Vankosoft\ApplicationBundle\Component\Context\ApplicationContextInterface;
use Vankosoft\UsersBundle\Security\UserManager;
use Vankosoft\UsersBundle\Security\AnotherLoginFormAuthenticator;
use Symfony\Component\IntlSubdivision\IntlSubdivision;

use App\Entity\UserManagement\UserInfo;

class RegisterController extends BaseRegisterController
{
    use GlobalFormsTrait;
    
    /** @var Factory */
    private $paidSubscriptionFactory;
    
    /** @var RepositoryInterface */
    private $pricingPlanRepository;
    
    public function __construct(
        ManagerRegistry $doctrine,
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
        RepositoryInterface $pricingPlanRepository
    ) {
        parent::__construct(
            $doctrine,
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
            
            $oUser->addRole( $this->userRolesRepository->findByTaxonCode( $this->params['registerRole'] ) );
            $oUser->addApplication( $this->applicationContext->getApplication() );
            
            $preferedLocale = $form->get( "prefered_locale" )->getData();
            $oUser->setPreferedLocale( $preferedLocale );
            $oUser->setVerified( false );
            $oUser->setEnabled( false );
            
            /** Populate UserInfo Values */
            $oUser->getInfo()->setFirstName( $form->get( "firstName" )->getData() );
            $oUser->getInfo()->setLastName( $form->get( "lastName" )->getData() );
            $oUser->getInfo()->setBirthday( $form->get( "birthday" )->getData() );
            
            $this->setPricingPlan( $oUser, $form );
            
            $em->persist( $oUser );
            $em->flush();
            
            $this->sendConfirmationMail( $oUser, $mailer );
            $this->addMessages( $request );
            
            return $this->redirectToRoute( $this->params['defaultRedirect'] );
        }
    }
    
    protected function addMessages( Request $request )
    {
        $this->addFlash(
            'success',
            'Your registration has been created !'
        );
    }
    
    private function setPricingPlan( &$user, $form ): void
    {
        $pricingPlanId  = $form->get( "pricingPlan" )->getData();
        $pricingPlan    = $this->pricingPlanRepository->find( $pricingPlanId );
        
        if( $pricingPlan ) {
            $em                 = $this->doctrine->getManager();
            $paidServicePeriod  = $pricingPlan->getPaidServicePeriod();
            $paidSubscription   = $this->paidSubscriptionFactory->createNew();
            
            $paidSubscription->setPayedService( $paidServicePeriod );
            $paidSubscription->setUser( $user );
            $paidSubscription->setDate( new \DateTime() );
            $paidSubscription->setSubscriptionCode( $paidServicePeriod->getPayedService()->getSubscriptionCode() );
            $paidSubscription->setSubscriptionPriority( $paidServicePeriod->getPayedService()->getSubscriptionPriority() );
            
            $em->persist( $paidSubscription );
            $em->flush();
            
            $user->addPaidSubscription( $paidSubscription );
        }
    }
}
