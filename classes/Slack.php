<?php

use Slack\Bot;

class SlackSecure extends Bot
{
    private $objConfig;
    private $objApi;
    private $arrUsers;

    private $arrScamDomains;

    public function __construct()
    {
        $this->objConfig = parse_ini_file(__DIR__ ."/../config.ini", true);
        parent::__construct($this->objConfig["app"]["token"]);
        $this->objApi = new \CL\Slack\Transport\ApiClient($this->objConfig["app"]["token"]);

        $this->arrScamDomains = [1];
    }

    /**
     * Handles the messages from Slacks' RTM
     * @param \Ratchet\RFC6455\Messaging\MessageInterface $msg
     * @return mixed
     */
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
                       file_put_contents("php://stderr", $arrMessage["type"] . " handler not implemented!" . PHP_EOL);
                   }
                   break;
           }
       }
    }

    /**
     * Handles reconnect_url message
     * @param   array       $arrMessage     The payload from Slack
     */
    public function reconnect_url(array $arrMessage)
    {
        if($this->objConfig["app"]["debug"]) {
            file_put_contents("php://stderr", "[RECONNECT] URL Received" . PHP_EOL);
        }
    }

    public function hello(array $arrMessage)
    {
        file_put_contents("php://stderr", PHP_EOL . PHP_EOL . "\e[33m--- CONNECTED SUCCESSFULLY ---\e[0m". PHP_EOL . PHP_EOL);
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
            file_put_contents("php://stderr", "\e[33mMESSAGE\e[0m " . $arrMessage['user'] . " posted a message to channel: " . $arrMessage['channel'] . PHP_EOL);
        }

        //Delete reminders to everyone
        if (preg_match("/\<!everyone\>/", $arrMessage["text"])) {
            $this->doMessageDelete($arrMessage, "REMINDER");
        }

        //Delete Slackbot
        if ($arrMessage["user"] === "USLACKBOT") {
            $this->doMessageDelete($arrMessage, "SLACKBOT");
        }

        //Process + commands
        if (preg_match("/^\+/", $arrMessage["text"])) {
            $this->doMessageCommand($arrMessage);
        }
    }

    /**
     * Handles a bot being added
     * @param   array       $arrMessage     The payload from Slack
     */
    public function bot_added(array $arrMessage)
    {
        echo "\e[33mBOT\e[0m Bot '". $arrMessage["bot"]["name"] ."' was added.";
        //@todo - see if we can revoke the token
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
            file_put_contents("php://stderr", "\t \e[33mSUCCESS\e[0m MESSAGE WAS DELETED (". $strReason .")" . PHP_EOL);
        } else {
            file_put_contents("php://stderr", "\t \e[91mFAILED\e[0m Unable to delete reminder: ". $objResponse->getError() . PHP_EOL);
        }
    }

    /**
     * Handles subtype messages
     * @param   array       $arrMessage     The payload from Slack
     * @return   bool
     */
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

    private function doMessageCommand(array $arrMessage)
    {
        switch(ltrim($arrMessage["text"], "+")) {
            //get your user id for config settings
            case 'userid' :
                if($this->objConfig["admin"]["userid"] == "") {
                    file_put_contents("php://stderr", PHP_EOL . "\e[91mCOMMAND\e[0m Userid: " . $arrMessage["user"] . PHP_EOL);
                }
                break;
            //update the scam domains
            case 'update' :
                $this->getAllUsers();
                $objGuzzle = new GuzzleHttp\Client();
                $objResponse = $objGuzzle->request("GET", "https://etherscamdb.info/data/scams.json");

                if($this->objConfig["admin"]["userid"] == $arrMessage["user"]) {
                    if ($objResponse->getStatusCode() == 200) {
                        $this->arrScamDomains = json_decode($objResponse->getBody()->getContents(), true);
                        file_put_contents("php://stderr", PHP_EOL . "\e[91mCOMMAND\e[0m Updated domains: " . number_format(count($this->arrScamDomains)) . PHP_EOL);
                    } else {
                        file_put_contents("php://stderr", PHP_EOL . "\e[91mCOMMAND\e[0m Failed to update domains: HTTP" . $objResponse->getStatusCode() . PHP_EOL);
                    }
                } else {
                    file_put_contents("php://stderr", PHP_EOL . "\e[91mCOMMAND\e[0m Failed to update domains: set your admin[userid] config item." . PHP_EOL);
                }
                break;
            //invites everyone to the channel
            case 'invite-all' :
                $this->getAllUsers();
                foreach($this->arrUsers as $arrUser) {
                    if($arrUser["is_bot"] xor $arrUser["deleted"]) {
                        continue;
                    }
                    $objPayload = new \CL\Slack\Payload\ChannelsInvitePayload();
                    $objPayload->setChannelId($arrMessage["channel"]);
                    $objPayload->setUserId($arrUser["id"]);

                    $objResponse = $this->objApi->send($objPayload);
                    if ($objResponse->isOk()) {
                        file_put_contents("php://stderr", "\t \e[33mSUCCESS\e[0m INVITED ". $arrUser["name"] ." TO CHANNEL" . PHP_EOL);
                    } else {
                        if($this->objConfig["app"]["debug"]) {
                            file_put_contents("php://stderr", PHP_EOL . "\e[91mCOMMAND\e[0m Failed to invite  " . $arrUser["name"] . ". - " . json_encode($objResponse->getError()) . PHP_EOL);
                        }
                    }
                }
                file_put_contents("php://stderr", PHP_EOL . "\e[91mCOMMAND\e[0m DONE." . PHP_EOL);
                break;
            default:
                return true;
                break;
        }

        //Then delete that message
        $this->doMessageDelete($arrMessage, "COMMAND MESSAGE");
    }

    /**
     * Fetches all the users and stores it in the class property
     */
    private function getAllUsers()
    {
        $objPayload = new \CL\Slack\Payload\UsersListPayload();
        $objResponse = $this->objApi->send($objPayload);

        if($objResponse->isOk())
        {
            $arrUsers = $objResponse->getUsers();
            foreach($arrUsers as $objUser)
            {
                $this->arrUsers[] = [
                    "id" => $objUser->getId(),
                    "name" => $objUser->getName(),
                    "is_admin" => (bool) $objUser->isAdmin(),
                    "is_bot" => (bool) $objUser->isBot(),
                    "deleted" => (bool) $objUser->isDeleted()
                ];
            }
        } else {
            file_put_contents("php://stderr", PHP_EOL . "\e[91mCOMMAND\e[0m Failed to get users. - ". json_encode($objResponse->getError()) . PHP_EOL);
        }
    }
}