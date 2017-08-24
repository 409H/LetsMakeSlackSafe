<?php

use Slack\Bot;

class SlackSecure extends Bot
{
    private $objConfig;
    private $objApi;

    public function __construct()
    {
        $this->objConfig = parse_ini_file(__DIR__ ."/../config.ini", true);
        parent::__construct($this->objConfig["app"]["token"]);
        $this->objApi = new \CL\Slack\Transport\ApiClient($this->objConfig["app"]["token"]);
    }

    public function handleMessage(\Ratchet\RFC6455\Messaging\MessageInterface $msg)
    {
       $arrMessage = json_decode($msg, true);

       if(isset($arrMessage["type"])) {
           switch ($arrMessage["type"]) {
               case method_exists($this, $arrMessage["type"]) :
                   return $this->{$arrMessage["type"]}($arrMessage);
                   break;
               default:
                   if($this->objConfig["app"]["debug"]) {
                       echo $arrMessage["type"] . " handler not implemented!" . PHP_EOL;
                   }
                   break;
           }
       } else {
           print_r($arrMessage);
       }
    }

    /**
     * Handles reconnect_url message
     * @param   array       $arrMessage     The payload from Slack
     */
    public function reconnect_url(array $arrMessage)
    {
        echo "[RECONNECT] URL Received". PHP_EOL;
    }

    /**
     * Handles message message
     * @param   array       $arrMessage     The payload from Slack
     */
    public function message(array $arrMessage)
    {
        if(isset($arrMessage["subtype"])) {
            $this->doSubtypeMessage($arrMessage);
            return;
        }

        if($this->objConfig["app"]["debug"]) {
            echo "\e[33mMESSAGE\e[0m " . $arrMessage['user'] . " posted a message to channel: " . $arrMessage['channel'] . PHP_EOL;
        }

        //Delete reminders
        if (preg_match("/\<!everyone\>/", $arrMessage["text"])) {
            $this->doMessageDelete($arrMessage, "REMINDER");
        }

        //Delete Slackbot
        if ($arrMessage["user"] === "USLACKBOT") {
            $this->doMessageDelete($arrMessage, "SLACKBOT");
        }
    }

    /**
     * Handles a bot being added
     * @param   array       $arrMessage     The payload from Slack
     */
    public function bot_added(array $arrMessage)
    {
        echo "\e[33mBOT\e[0m Bot '". $arrMessage["bot"]["name"] ."' was added.";
        $this->revokeToken($arrMessage);
    }

    /**
     * Deletes a remind message
     * @param   array       $arrMessage     The payload from Slack.
     * @param   string      $strReason      A reason why the message was deleted.
     */
    private function doMessageDelete(array $arrMessage, $strReason)
    {
        $objPayload = new \CL\Slack\Payload\ChatDeletePayload();
        $objPayload->setChannelId($arrMessage["channel"]);
        $objPayload->setSlackTimestamp($arrMessage["ts"]);

        $objResponse = $this->objApi->send($objPayload);
        if($objResponse->isOk()) {
            echo "\t \e[33mSUCCESS\e[0m MESSAGE WAS DELETED (". $strReason .")" . PHP_EOL;
        } else {
            echo "\t \e[91mFAILED\e[0m Unable to delete reminder: ". $objResponse->getError() . PHP_EOL;
        }
    }

    private function doSubtypeMessage(array $arrMessage)
    {
        if(isset($arrMessage["bot_id"]) === false OR $arrMessage['subtype'] === 'message_deleted') {
            return true;
        }

        //Bot isn't whitelisted!
        if(in_array($arrMessage['bot_id'], (explode(",", $this->objConfig["whitelist"]["bot_ids"]))) === false) {
            $this->doMessageDelete($arrMessage, "BOT MESSAGE");
        } else {
            echo"\t BOT '". $arrMessage['username'] ."' (". $arrMessage['bot_id'] .") IS WHITELISTED." . PHP_EOL;
        }

    }

    private function revokeToken(array $arrMessage)
    {
        print_r($arrMessage);
        echo "Revoked Token";
        die;

        $objPayload = new \CL\Slack\Payload\AuthRevokePayload();
        $objPayload->setToken("xoxp-231275571699-231451258836-230781957568-d4d7e3276a4cfbc2a798afc6dddb77bd");

        $objResponse = $this->objApi->send($objPayload);
        if($objResponse->isOk()) {
            echo "\t \e[33mSUCCESS\e[0m TOKEN WAS REVOKED" . PHP_EOL;
        } else {
            echo "\t \e[91mFAILED\e[0m Unable to revoke token: ". $objResponse->getError() . PHP_EOL;
        }
    }
}