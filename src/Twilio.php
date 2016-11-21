<?php

namespace NotificationChannels\Twilio;

use NotificationChannels\Twilio\Exceptions\CouldNotSendNotification;
use Twilio\Rest\Client as TwilioClient;

class Twilio
{
    /**
     * @var TwilioClient
     */
    protected $twilioClient;

    /**
     * Default 'from' from config.
     * @var string
     */
    protected $from;

    /**
     * Twilio constructor.
     *
     * @param  TwilioClient  $twilioClient
     * @param  string  $from
     */
    public function __construct(TwilioClient $twilioClient, $from)
    {
        $this->twilioClient = $twilioClient;
        $this->from = $from;
    }

    /**
     * Send a TwilioMessage to the a phone number.
     *
     * @param  TwilioMessage  $message
     * @param  $to
     * @return mixed
     * @throws CouldNotSendNotification
     */
    public function sendMessage(TwilioMessage $message, $to)
    {
        if ($message instanceof TwilioSmsMessage) {
            return $this->sendSmsMessage($message, $to);
        }

        // TODO: Enable MakeCall
//        if ($message instanceof TwilioCallMessage) {
//            return $this->makeCall($message, $to);
//        }

        throw CouldNotSendNotification::invalidMessageObject($message);
    }

    protected function sendSmsMessage($message, $to)
    {
        return $this->twilioClient->messages->create(
            $to,
            [
                'from' => $this->getFrom($message),
                'body' => trim($message->content),
            ]
        );
    }

    // TODO: Enable MakeCall
//    protected function makeCall($message, $to)
//    {
//        return $this->twilioCLient->account->calls->create(
//            $this->getFrom($message),
//            $to,
//            trim($message->content)
//        );
//    }

    protected function getFrom($message)
    {
        if (! $from = $message->from ?: $this->from) {
            throw CouldNotSendNotification::missingFrom();
        }

        return $from;
    }
}
