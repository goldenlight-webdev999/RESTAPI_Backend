<?php


namespace App\Infrastructure\Paypal;

use App\Infrastructure\Paypal\Entity\Customer;
use App\Infrastructure\Paypal\Entity\CustomerSubscription;
use App\Infrastructure\Paypal\Repository\CustomerRepository;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\User;
use PayPal\Api\ChargeModel;
use PayPal\Api\Currency;
use PayPal\Api\MerchantPreferences;
use PayPal\Api\PaymentDefinition;
use PayPal\Api\Plan;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

final class PaypalAdapter
{
    private $security;
    private $userRepository;
    private $customerRepository;
    private $appUrl;
    private $paypalClientId;
    private $paypalSecret;
    private $paypalWebhookId;

    public function __construct(
        string $appUrl,
        string $paypalClientId,
        string $paypalSecret,
        string $paypalWebhookId,
        Security $security,
        UserRepositoryInterface $userRepository,
        CustomerRepository $customerRepository
    )
    {
        $this->security = $security;
        $this->userRepository = $userRepository;
        $this->customerRepository = $customerRepository;
        $this->appUrl = $appUrl;
        $this->paypalClientId = $paypalClientId;
        $this->paypalSecret = $paypalSecret;
        $this->paypalWebhookId = $paypalWebhookId;
    }


    public function isValidWebhookRequest(Request $request): bool
    {
        $body=file_get_contents('php://input');
        try {

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://api-m.sandbox.paypal.com/v1/notifications/verify-webhook-signature');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);

            $headers = array();
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Authorization: Bearer '.$this->getPaypalAuthToken();
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $data = [
                "auth_algo"=>$request->headers->get("PAYPAL-AUTH-ALGO"),
                "cert_url"=>$request->headers->get("PAYPAL-CERT-URL"),
                "transmission_id"=>$request->headers->get("PAYPAL-TRANSMISSION-ID"),
                "transmission_sig"=>$request->headers->get("PAYPAL-TRANSMISSION-SIG"),
                "transmission_time"=>$request->headers->get("PAYPAL-TRANSMISSION-TIME"),
                "webhook_id"=>$this->paypalWebhookId,
                "webhook_event"=>json_decode($request->getContent(), true)
            ];

            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));


            $result = curl_exec($ch);
            curl_close($ch);

            $result = json_decode($result, true);



            if(isset($result['verification_status'])){
                if($result['verification_status'] == "SUCCESS"){
                    return true;
                }
            }

        } catch (\Exception $e) {
            /**
             * Invalid webhook: there is a problem with the content format or the webhook signature
             */
            return false;
        }
        return false;
    }

    public function getPaypalAuthToken()
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api-m.sandbox.paypal.com/v1/oauth2/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
        curl_setopt($ch, CURLOPT_USERPWD, $this->paypalClientId . ':' . $this->paypalSecret);

        $headers = array();
        $headers[] = 'Accept: application/json';
        $headers[] = 'Accept-Language: en_US';
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($result);

        if(isset($result->access_token)){
            return $result->access_token;
        }
        return false;
    }

    public function cancelSubscription(\App\Infrastructure\Paypal\Entity\CustomerSubscription $subscription): void
    {
        /**
         * @var Subscription $paypalSubscription
         */

        $token = $this->getPaypalAuthToken();
        $post_data = [
            "reason"=>"Cancelled Via Site"
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api-m.sandbox.paypal.com/v1/billing/subscriptions/'.$subscription->getSubscriptionId()."/cancel");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: Bearer '.$token;
        $headers[] = 'Paypal-Request-Id: SUBSCRIPTION-21092019-001';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

    }

    public function createSubscription($plan_id)
    {
        $token = $this->getPaypalAuthToken();
        $uuid = $this->security->getUser()->getId();
        $email = (string) $this->userRepository->get($uuid)->getEmail();
        $post_data = [
            "plan_id"=>$plan_id,
            "custom_id"=>$email,
            "subscriber"=>[
                "email_address" => $email,
            ],
            "application_context"=>[
                "user_action"=>"SUBSCRIBE_NOW",
                "payment_method"=> [
                    "payer_selected"=> "PAYPAL",
                    "payee_preferred"=> "IMMEDIATE_PAYMENT_REQUIRED"
                ],
                "return_url" =>  $this->appUrl."/dashboard/subscriptions",
                "cancel_url" => $this->appUrl."/dashboard/subscription?cancelled=true",
            ]
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api-m.sandbox.paypal.com/v1/billing/subscriptions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: Bearer '.$token;
        $headers[] = 'Paypal-Request-Id: SUBSCRIPTION-21092019-001';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        return $result;
    }

}