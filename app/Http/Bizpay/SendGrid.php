<?php

namespace App\Http\Bizpay;

use SendGrid\Content;
use SendGrid\Email;
use SendGrid\Mail;
use SendGrid\ReplyTo;

/**
 * Class SendGrid
 * @package App\Http\Bizpay
 */
class SendGrid{

    private $sg;

    /**
     * SendGrid constructor.
     */
    public function __construct()
    {
        $apiKey = env('SENDGRID');
        $this->sg = new \SendGrid($apiKey);
    }

    /**
     * @param $name
     * @param $agreementId
     * @param $merchantName
     * @param $clientEmail
     */
    public function paymentMissed($merchantWebsite,$clientName,$merchantLogoFile,$clientEmail, $agreementId, $merchantName,$merchantEmail, $amount,$currencyCode,$date)
    {
        $from = new Email(null, "no-reply@bizpay.co.uk");
        $subject = "Payment missed - Please pay now - ".$merchantName;
        $to = new Email(null, $clientEmail);
        $content = new Content("text/html", "Payment Request");
        $mail = new Mail($from, $subject, $to, $content);
        $reply_to = new ReplyTo($merchantEmail, $merchantName);
        $mail->setReplyTo($reply_to);
        $mail->personalization[0]->addSubstitution("#:amount", $amount);
        $mail->personalization[0]->addSubstitution("#:payment-currency", $currencyCode);
        $mail->personalization[0]->addSubstitution("#:date", $date);
        $mail->personalization[0]->addSubstitution("#:name", $clientName);
        $mail->personalization[0]->addSubstitution("#:agreement-id", $agreementId);
        $mail->personalization[0]->addSubstitution("#:merchant-name", $merchantName);
        $mail->personalization[0]->addSubstitution("#:merchant-website", $merchantWebsite);
        $mail->personalization[0]->addSubstitution("#:merchant-logo-file", $merchantLogoFile);
        $mail->setTemplateId("99845ef5-42ad-4e12-b38a-f99f6a4f5867");
        $this->sendMail($mail);
    }

    public function paymentSucceeded($merchantWebsite,$clientName,$merchantLogoFile,$clientEmail, $agreementId, $merchantName,$merchantEmail, $amount,$currencyCode,$date)
    {
        $from = new Email(null, "no-reply@bizpay.co.uk");
        $subject = "Payment Succeeded ".$merchantName;
        $to = new Email(null, $clientEmail);
        $content = new Content("text/html", "Payment Request");
        $mail = new Mail($from, $subject, $to, $content);
        $reply_to = new ReplyTo($merchantEmail, $merchantName);
        $mail->setReplyTo($reply_to);
        $mail->personalization[0]->addSubstitution("#:amount", $amount);
        $mail->personalization[0]->addSubstitution("#:payment-currency", $currencyCode);
        $mail->personalization[0]->addSubstitution("#:date", $date);
        $mail->personalization[0]->addSubstitution("#:name", $clientName);
        $mail->personalization[0]->addSubstitution("#:agreement-id", $agreementId);
        $mail->personalization[0]->addSubstitution("#:merchant-name", $merchantName);
        $mail->personalization[0]->addSubstitution("#:merchant-website", $merchantWebsite);
        $mail->personalization[0]->addSubstitution("#:merchant-logo-file", $merchantLogoFile);
        $mail->setTemplateId("99845ef5-42ad-4e12-b38a-f99f6a4f5867");
        $this->sendMail($mail);
    }

    /**
     * @param $name
     * @param $agreementId
     * @param $merchantName
     * @param $clientEmail
     */
    public function paymentReminder($name, $agreementId, $merchantName, $clientEmail, $amount,$currencyCode,$date)
    {
        $from = new Email(null, "no-reply@bizpay.co.uk");
        $subject = "Payment missed - Please pay now - ".$merchantName;
        $to = new Email(null, $clientEmail);
        $content = new Content("text/html", "Payment Request");
        $mail = new Mail($from, $subject, $to, $content);
        $mail->personalization[0]->addSubstitution("#:amount", $amount);
        $mail->personalization[0]->addSubstitution("#:payment-currency", $currencyCode);
        $mail->personalization[0]->addSubstitution("#:date", $date);
        $mail->personalization[0]->addSubstitution("#:name", $name);
        $mail->personalization[0]->addSubstitution("#:agreement-id", $agreementId);
        $mail->personalization[0]->addSubstitution("#:merchant-name", $merchantName);
        $mail->setTemplateId("99845ef5-42ad-4e12-b38a-f99f6a4f5867");
        $this->sendMail($mail);
    }

    /**
     * @param $resetLink
     * @param $merchantEmail
     * @param $merchantName
     * @param $clientEmail
     * @param $merchantWebsite
     * @param $merchantLogoFile
     */
    public function passwordReset(
        $resetLink,
        $merchantEmail,
        $merchantName,
        $clientEmail,
        $merchantWebsite,
        $merchantLogoFile
    )
    {
        $from = new Email(null, "no-reply@bizpay.co.uk");
        $subject = "Password Reset";
        $to = new Email(null, $clientEmail);
        $content = new Content("text/html", "Password Reset");
        $mail = new Mail($from, $subject, $to, $content);
        $reply_to = new ReplyTo($merchantEmail, $merchantName);
        $mail->setReplyTo($reply_to);
        $mail->personalization[0]->addSubstitution("#:merchant-first-name", $merchantName);
        $mail->personalization[0]->addSubstitution("#:merchant-website", $merchantWebsite);
        $mail->personalization[0]->addSubstitution("#:merchant-logo-file", $merchantLogoFile);
        $mail->personalization[0]->addSubstitution("#:merchant-password-reset-link", $resetLink);
        $mail->setTemplateId("8ba86a9a-69c2-4a41-9def-d01e2201110d");
        $this->sendMail($mail);
    }

    public function registrationEmail($firstName, $clientEmail)
    {
        $from = new Email(null, "no-reply@bizpay.co.uk");
        $subject = "Welcome to bizpay";
        $to = new Email(null, $clientEmail);
        $content = new Content("text/html", "Bizpay");
        $mail = new Mail($from, $subject, $to, $content);
        $reply_to = new ReplyTo("info@bizpay.co.uk", "bizpay");
        $mail->setReplyTo($reply_to);
        $mail->personalization[0]->addSubstitution("#:client-first-name", $firstName);
        $mail->personalization[0]->addSubstitution("#:client-login-link", "https://app.bizpay.co.uk/login");

        $mail->setTemplateId("531f7cf6-c0be-40d6-9b20-a3f678fc9c87");
        $this->sendMail($mail);
    }

    /**
     * @param $clientEmail
     * @param $merchantName
     * @param $clientName
     * @param $agreementTotal
     * @param $instalmentPeriod
     * @param $instalments
     */
    public function newAgreement($clientEmail,$merchantName,$clientName,$agreementTotal,$instalmentPeriod,$instalments)
    {
        $from = new Email(null, "no-reply@bizpay.co.uk");
        $subject = "Welcome to bizpay";
        $to = new Email(null, $clientEmail);
        $content = new Content("text/html", "Bizpay");
        $mail = new Mail($from, $subject, $to, $content);
        $reply_to = new ReplyTo("info@bizpay.co.uk", "Bizpay");
        $mail->setReplyTo($reply_to);

        $mail->personalization[0]->addSubstitution("#:merchant-first-name", $merchantName);
        $mail->personalization[0]->addSubstitution("#:buyer-full-name", $clientName);
        $mail->personalization[0]->addSubstitution("#:agreement-value", $agreementTotal);
        $mail->personalization[0]->addSubstitution("#:installment-period", $instalmentPeriod);
        $mail->personalization[0]->addSubstitution("#:number-of-installments", $instalments);

        $mail->setTemplateId("22abd3f7-6e6b-47c7-bc07-3f11525f80d7");
        $this->sendMail($mail);
    }

    /**
     * @param $clientEmail
     * @param $clientName
     * @param $agreementTotal
     * @param $instalmentPeriod
     * @param $instalments
     */
    public function agreementCancelled($clientEmail,$clientName,$agreementTotal,$instalmentPeriod,$instalments)
    {
        $from = new Email(null, "no-reply@bizpay.co.uk");
        $subject = "Agreement Cancelled";
        $to = new Email(null, $clientEmail);
        $content = new Content("text/html", "Bizpay");
        $mail = new Mail($from, $subject, $to, $content);
        $reply_to = new ReplyTo("info@bizpay.co.uk", "Bizpay");
        $mail->setReplyTo($reply_to);

        $mail->personalization[0]->addSubstitution("#:merchant-website", $clientName);
        $mail->personalization[0]->addSubstitution("#:merchant-logo-file", $agreementTotal);
        $mail->personalization[0]->addSubstitution("#:installment-period", $instalmentPeriod);
        $mail->personalization[0]->addSubstitution("#:number-of-installments", $instalments);

        $mail->setTemplateId("a5de8905-b7c4-42c0-a507-4ff55af1fc76");
        $this->sendMail($mail);
    }

    /**
     * @param $clientEmail
     * @param $clientName
     * @param $agreementTotal
     * @param $instalmentPeriod
     * @param $instalments
     */
    public function agreementRenewed($clientEmail,$clientName,$agreementTotal,$instalmentPeriod,$instalments)
    {
        $from = new Email(null, "no-reply@bizpay.co.uk");
        $subject = "Agreement Renewed";
        $to = new Email(null, $clientEmail);
        $content = new Content("text/html", "Bizpay");
        $mail = new Mail($from, $subject, $to, $content);
        $reply_to = new ReplyTo("info@bizpay.co.uk", "Bizpay");
        $mail->setReplyTo($reply_to);

        $mail->personalization[0]->addSubstitution("#:merchant-website", $clientName);
        $mail->personalization[0]->addSubstitution("#:merchant-logo-file", $agreementTotal);
        $mail->personalization[0]->addSubstitution("#:installment-period", $instalmentPeriod);
        $mail->personalization[0]->addSubstitution("#:number-of-installments", $instalments);

        $mail->setTemplateId("a5de8905-b7c4-42c0-a507-4ff55af1fc76");
        $this->sendMail($mail);
    }


    /**
     * @param $merchantEmail
     * @param $merchantName
     * @param $clientEmail
     */
    public function agreementCreatedClientEmail($merchantEmail, $merchantName,$clientEmail)
    {

        $from = new Email(null, "no-reply@bizpay.co.uk");
        $subject = "Agreement Created";
        $to = new Email(null, $clientEmail);
        $content = new Content("text/html", "Agreement");
        $mail = new Mail($from, $subject, $to, $content);
        $reply_to = new ReplyTo($merchantEmail, $merchantName);
        $mail->setReplyTo($reply_to);

        $mail->personalization[0]->addSubstitution("#:merchant-first-name", $merchantName);


        $mail->setTemplateId("8ba86a9a-69c2-4a41-9def-d01e2201110d");
        $this->sendMail($mail);

    }

    /**
     * @param $merchantEmail
     * @param $merchantName
     * @param $merchantLogo
     * @param $merchantWebsite
     * @param $clientEmail
     * @param $clientName
     * @param $agreementId
     */
    public function cardExpired(
        $merchantEmail,
        $merchantName,
        $merchantLogo,
        $merchantWebsite,
        $clientEmail,
        $clientName,
        $agreementId
    )
    {

        $from = new Email(null, "no-reply@bizpay.co.uk");
        $subject = "Card Expired";
        $to = new Email(null, $clientEmail);
        $content = new Content("text/html", "Agreement");
        $mail = new Mail($from, $subject, $to, $content);
        $reply_to = new ReplyTo($merchantEmail, $merchantName);
        $mail->setReplyTo($reply_to);

        $mail->personalization[0]->addSubstitution("#:buyer-first-name", $clientName);
        $mail->personalization[0]->addSubstitution("#:buyer-agreement-link", "https://app.bizpay.co.uk/agreements/".$agreementId);
        $mail->personalization[0]->addSubstitution("#:merchant-full-name", $merchantName);
        $mail->personalization[0]->addSubstitution("#:merchant-logo-file", $merchantLogo);
        $mail->personalization[0]->addSubstitution("#:merchant-website", $merchantWebsite);

        $mail->setTemplateId("7ac35206-b3d9-487e-8527-504d98636a82");
        $this->sendMail($mail);

    }

    /**
     * @param $clientEmail
     * @param $merchantEmail
     * @param $merchantName
     */
    public function agreementCancelledClientEmail($clientEmail,$merchantEmail, $merchantName)
    {

        $from = new Email(null, "no-reply@bizpay.co.uk");
        $subject = "Agreement Cancelled";
        $to = new Email(null, $clientEmail);
        $content = new Content("text/html", "Agreement");
        $mail = new Mail($from, $subject, $to, $content);
        $reply_to = new ReplyTo($merchantEmail, $merchantName);
        $mail->setReplyTo($reply_to);

        $mail->personalization[0]->addSubstitution("#:merchant-first-name", $merchantName);


        $mail->setTemplateId("8ba86a9a-69c2-4a41-9def-d01e2201110d");
        $this->sendMail($mail);

    }

    /**
     * @param $clientEmail
     * @param $merchantEmail
     * @param $merchantName
     */
    public function addPaymentInfoReminder($clientEmail,$merchantEmail, $merchantName)
    {

        $from = new Email(null, "no-reply@bizpay.co.uk");
        $subject = "Payment Information Required";
        $to = new Email(null, $clientEmail);
        $content = new Content("text/html", "Agreement");
        $mail = new Mail($from, $subject, $to, $content);
        $reply_to = new ReplyTo($merchantEmail, $merchantName);
        $mail->setReplyTo($reply_to);

        $mail->personalization[0]->addSubstitution("#:merchant-first-name", $merchantName);


        $mail->setTemplateId("8ba86a9a-69c2-4a41-9def-d01e2201110d");
        $this->sendMail($mail);

    }

    /**
     * @param $merchantEmail
     * @param $merchantName
     * @param $merchantLogo
     * @param $merchantWebsite
     * @param $clientEmail
     * @param $clientName
     * @param $currency
     * @param $amount
     * @param $date
     * @param $agreementId
     */
    public function noPaymentEmail( $merchantEmail, $merchantName,$merchantLogo,$merchantWebsite,$clientEmail,$clientName,$currency,$amount,$date,$agreementId)
    {

        $from = new Email(null, "no-reply@bizpay.co.uk");
        $subject = "Payment Method";
        $to = new Email(null, $clientEmail);
        $content = new Content("text/html", "Agreement");
        $mail = new Mail($from, $subject, $to, $content);
        $reply_to = new ReplyTo($merchantEmail, $merchantName);
        $mail->setReplyTo($reply_to);

        $mail->personalization[0]->addSubstitution("#:buyer-first-name", $clientName);
        $mail->personalization[0]->addSubstitution("#:payment-currency", $currency);
        $mail->personalization[0]->addSubstitution("#:amount", $amount);
        $mail->personalization[0]->addSubstitution("#:date", $date);
        $mail->personalization[0]->addSubstitution("#:merchant-full-name", $merchantName);
        $mail->personalization[0]->addSubstitution("#:buyer-agreement-link", "https://app.bizpay.co.uk/agreements/".$agreementId);
        $mail->personalization[0]->addSubstitution("#:merchant-logo-file", $merchantLogo);
        $mail->personalization[0]->addSubstitution("#:merchant-website", $merchantWebsite);


        $mail->setTemplateId("b8b04065-8711-4520-80fd-816ea428c7c3");
        $this->sendMail($mail);

    }


    /**
     * @param $clientEmail
     * @param $merchantEmail
     * @param $merchantName
     */
    public function addNewPaymentInfoEmail($clientEmail, $merchantEmail, $merchantName)
    {

        $from = new Email(null, "no-reply@bizpay.co.uk");
        $subject = "Payment Information";
        $to = new Email(null, $clientEmail);
        $content = new Content("text/html", "Agreement");
        $mail = new Mail($from, $subject, $to, $content);
        $reply_to = new ReplyTo($merchantEmail, $merchantName);
        $mail->setReplyTo($reply_to);

        $mail->personalization[0]->addSubstitution("#:merchant-first-name", $merchantName);


        $mail->setTemplateId("8ba86a9a-69c2-4a41-9def-d01e2201110d");
        $this->sendMail($mail);

    }

    /**
     * @param $merchantEmail
     * @param $merchantAdminName
     * @param $managerName
     * @param $managerEmail
     * @param $managerPassword
     */
    public function addMerchantManager($merchantEmail,$merchantAdminName,$managerName, $managerEmail, $managerPassword)
    {
        $from = new Email(null, "no-reply@bizpay.co.uk");
        $subject = "Payment gateway API details have changed";
        $to = new Email(null, $merchantEmail);
        $content = new Content("text/html", "Bizpay");
        $mail = new Mail($from, $subject, $to, $content);
        $reply_to = new ReplyTo("info@bizpay.co.uk", "Bizpay");
        $mail->setReplyTo($reply_to);

        $mail->personalization[0]->addSubstitution("#:merchant-user-first-name", $managerName);
        $mail->personalization[0]->addSubstitution("#:merchant-admin-full-name", $merchantAdminName);
        $mail->personalization[0]->addSubstitution("#:merchant-user-email", $managerEmail);
        $mail->personalization[0]->addSubstitution("#:merchant-user-password", $managerPassword);
        $mail->personalization[0]->addSubstitution("#:merchant-login-link", "https://app.bizpay.co.uk/login");

        $mail->setTemplateId("c90d242d-0625-4968-bf23-2464363dccbd");
        $this->sendMail($mail);
    }

    /**
     * @param $merchantEmail
     * @param $merchantAdminName
     */
    public function apiCredentialChanged($merchantEmail,$merchantAdminName)
    {
        $from = new Email(null, "no-reply@bizpay.co.uk");
        $subject = "Payment gateway API details have changed";
        $to = new Email(null, $merchantEmail);
        $content = new Content("text/html", "Bizpay");
        $mail = new Mail($from, $subject, $to, $content);
        $reply_to = new ReplyTo("info@bizpay.co.uk", "Bizpay");
        $mail->setReplyTo($reply_to);

        $mail->personalization[0]->addSubstitution("#:merchant-admin-first-name", $merchantAdminName);

        $mail->setTemplateId("f0a4055f-01fd-44ae-9e2a-ab2ffcef4a00");
        $this->sendMail($mail);
    }

    /**
     * @param $clientEmail
     * @param $clientName
     * @param $quoteId
     */
    public function sendQuote($clientEmail,$clientName,$quoteId)
    {
        $from = new Email(null, "no-reply@bizpay.co.uk");
        $subject = "Quote from bizpay";
        $to = new Email(null, $clientEmail);

        $quoteContent = " Hello ".$clientName.", <br>
                Purchase the quote at : <br>
                http://app.bizpay.co.uk/buy/".$quoteId."
        ";

        $content = new Content("text/html", $quoteContent);
        $mail = new Mail($from, $subject, $to, $content);
        $reply_to = new ReplyTo("info@bizpay.co.uk", "Bizpay");
        $mail->setReplyTo($reply_to);

        $this->sendMail($mail);
    }


    /**
     * @param $mail
     * @return bool
     */
    private function sendMail($mail)
    {
        try {
            $this->sg->client->mail()->send()->post($mail);

            return true;
        } catch (\Exception $e) {

           return false;
        }
    }



}