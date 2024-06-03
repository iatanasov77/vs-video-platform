<?php namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Log\LoggerInterface;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

use Sylius\Component\Resource\Repository\RepositoryInterface;

use Vankosoft\ApplicationBundle\Component\Status;
use Vankosoft\UsersBundle\Component\UserNotifications;
use Vankosoft\ApiBundle\Security\ApiManager;
use Vankosoft\PaymentBundle\Component\Payum\Stripe\Api as StripeApi;

/**
 * MANUALS
 * ========
 * https://stripe.com/docs/webhooks
 * https://symfonycasts.com/screencast/stripe-level2/webhook-endpoint-setup
 * https://symfonycasts.com/screencast/stripe-level2/stripe-events-webhook
 * 
 * Test your webhook endpoint function
 * ====================================
 * https://stripe.com/docs/webhooks#test-webhook
 */
class RecurringPaymentsWebhookController extends AbstractController
{
    /** @var ManagerRegistry */
    private $doctrine;
    
    /** @var MailerInterface */
    private $mailer;
    
    /** @var UserNotifications */
    private $notifications;
    
    /** @var RepositoryInterface */
    private $subscriptionsRepository;
    
    /** @var ApiManager */
    private $apiManager;
    
    /** @var StripeApi */
    private $stripeApi;
    
    /** @var LoggerInterface */
    private $logger;
    
    /** @var string */
    private $projectDir;
    
    public function __construct(
        ManagerRegistry $doctrine,
        MailerInterface $mailer,
        UserNotifications $notifications,
        RepositoryInterface $subscriptionsRepository,
        ApiManager $apiManager,
        StripeApi $stripeApi,
        LoggerInterface $logger,
        string $projectDir
    ) {
        $this->doctrine                 = $doctrine;
        $this->mailer                   = $mailer;
        $this->notifications            = $notifications;
        $this->subscriptionsRepository  = $subscriptionsRepository;
        $this->apiManager               = $apiManager;
        $this->stripeApi                = $stripeApi;
        $this->logger                   = $logger;
        $this->projectDir               = $projectDir;
    }
    
    public function invoicePaymentSucceeded( Request $request ): JsonResponse
    {
        $gatewayData        = $request->getContent();
        $gatewayDataDecoded = \json_decode( $gatewayData, true );
        if ( $gatewayDataDecoded === null ) {
            throw new \Exception( 'Bad JSON body from Stripe!' );
        }
        
        // Debug Stripe Event
        \file_put_contents( $this->projectDir . '/var/RecurringPaymentSucceeded.log', $gatewayDataDecoded['id'] );
        
        $stripeEvent    = $this->stripeApi->getEvent( $gatewayDataDecoded['id'] );
        
        $response   = [
            'status'    => Status::STATUS_OK,
            'message'   => 'Stripe Notification Success !!!',
        ];
        
        return new JsonResponse( $response );
    }
    
    public function invoicePaymentFailed( Request $request ): JsonResponse
    {
        $gatewayData        = $request->getContent();
        $gatewayDataDecoded = \json_decode( $gatewayData, true );
        if ( $gatewayDataDecoded === null ) {
            throw new \Exception( 'Bad JSON body from Stripe!' );
        }
        
        // Debug Stripe Event
        \file_put_contents( $this->projectDir . '/var/RecurringPaymentFailed.log', $gatewayDataDecoded['id'] );
        
        $stripeEvent    = $this->stripeApi->getEvent( $gatewayDataDecoded['id'] );
        
        $response   = [
            'status'    => Status::STATUS_ERROR,
            'message'   => 'Invalid API Token !!!',
        ];
        
        return new JsonResponse( $response );
    }
    
    private function updateSubscription( $data )
    {
        $dataDecoded    = \json_decode( $data, true );
        
        $subscription   = $this->subscriptionsRepository->findOneBy( ['jobId' => $dataDecoded['job_id']] );
        $subscription->setStatus( $dataDecoded['event'] );
        
        $this->doctrine->getManager()->persist( $subscription );
        $this->doctrine->getManager()->flush();
    }
    
    private function sendNotification( $data )
    {
        $dataDecoded    = \json_decode( $data, true );
        
        $this->notifications->sentNotificationByRole( 'role-super-admin', 'Stripe Webhook', $dataDecoded['event'], $data );
    }
    
    private function sendEmail( $data, $contactEmail )
    {
        $email = ( new TemplatedEmail() )
                ->from( $data['email'] )
                ->to( $contactEmail )
                ->subject( 'You have Contact Email From Vankosoft.Org' )
                ->htmlTemplate( 'email/contact_email.html.twig' )
                ->context([
                    'fromName'      => $data['name'],
                    'emailSubject'  => $data['subject'],
                    'emailBody'     => $data['message'],
                ]);
        
        $this->mailer->send( $email );
    }
}