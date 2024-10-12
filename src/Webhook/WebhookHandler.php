<?php
namespace App\Webhook;

use Xsolla\SDK\Exception\Webhook\InvalidSignatureException;
use Xsolla\SDK\Webhook\Message\Message;
use Xsolla\SDK\Webhook\Message\NotificationTypeDictionary;
use Xsolla\SDK\Webhook\Message\PaymentMessage;
use Xsolla\SDK\Webhook\Message\RefundMessage;
use Xsolla\SDK\Webhook\Message\UserValidationMessage;
use Xsolla\SDK\Webhook\WebhookAuthenticator;
use Xsolla\SDK\Webhook\WebhookRequest;

class WebhookHandler {

    /**
     * @param $secretKey
     * @return Message
     * @throws InvalidSignatureException
     */
    public static function getMessage($secretKey) {
        $request = WebhookRequest::fromGlobals();
        $authenticator = new WebhookAuthenticator($secretKey);
        $authenticator->authenticateSignature($request);
        return Message::fromArray($request->toArray());
    }

    /**
     * @param Message $message
     * @return array{0: string, 1: int} - response body and status code
     */
    public static function handle(Message $message) {
        switch ($message->getNotificationType()) {
            case NotificationTypeDictionary::USER_VALIDATION:
                /**
                 * https://developers.xsolla.com/webhooks/operation/user-validation/
                 * @var UserValidationMessage $message
                 */
                $user = $message->getUser();
                // if string start like 'test_xsolla' (for webhook check)
                if (is_array($user) && strpos($user['id'], 'test_xsolla') === 0) {
                    return ['{"error": {"code": "INVALID_USER","message": "Invalid user"}}', 400];
                }
                break;

            case NotificationTypeDictionary::PAYMENT:
                /**
                 * https://developers.xsolla.com/webhooks/operation/payment/
                 * @var PaymentMessage $message
                 */
                return ['{"status": "Payment processed successfully"}', 200];

            case NotificationTypeDictionary::REFUND:
                /**
                 * https://developers.xsolla.com/webhooks/operation/refund/
                 * @var RefundMessage $message
                 */
                break;

        }

        return ['', 200];
    }
}
