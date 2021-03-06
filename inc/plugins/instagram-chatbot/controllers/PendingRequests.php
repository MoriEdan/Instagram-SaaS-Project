<?php
namespace Plugins\InstagramChatbot;
/**
 * Chatbot
 */
class PendingRequests
{
    const IDNAME = "instagram-chatbot";
    /**
     * Process
     */
    public function process($account_id)
    {
        $this->start_process($account_id);
    }

    private function start_process($account_id){
        $Account = \Controller::model("Account", $account_id);
        $this->getPendingMessageRequests($Account);
    }

     private function getPendingMessageRequests($Account){
        try {
          $Instagram = \InstagramController::login($Account);
          $inbox = $Instagram->direct->getPendingInbox();
          $threads = $inbox->getInbox()->getThreads();
          $thredIDs = $this->getThreadIDs($threads);
          if(count($thredIDs) > 0){
            $Instagram->direct->approvePendingThreads($thredIDs);
            $this->addAccountToCron($Account, $threads);
            echo "<br>".count($thredIDs)." message requests approved";
          } else {
            echo "<br>No new message requests";
          }
        } catch (\Exception $e) {
          echo "Error: " . $e->getMessage();
          require_once PLUGINS_PATH."/".self::IDNAME."/controllers/ChatbotCronController.php";
          $ChatbotCron = new ChatbotCronController;
          if ( strpos($e->getMessage(), "Re-login required") !== false ){
              $ChatbotCron->disableInstagramAccountWithError($Account->get("id"));
              $ChatbotCron->chatbotErrorLog($Account->get("id"), $e->getMessage(), 'Account Chatbot deactivated');
          } else if( strpos($e->getMessage(), "Challenge Required") !== false ){
              $ChatbotCron->disableInstagramAccountWithError($Account->get("id"));
              $ChatbotCron->chatbotErrorLog($Account->get("id"), $e->getMessage(), 'Account Chatbot deactivated');
          } else if( strpos($e->getMessage(), "Feedback Required") !== false ){
              $ChatbotCron->disableInstagramAccountWithError($Account->get("id"));
              $ChatbotCron->chatbotErrorLog($Account->get("id"), $e->getMessage(), 'Account Chatbot deactivated');
          } else {
              $ChatbotCron->chatbotErrorLog($Account->get("id"), $e->getMessage(), 'ChatBot Will Retry');
          }
        }
     }

     private function getThreadIDs($threads){
       $ids = [];
       foreach($threads as $thread){
        array_push($ids, $thread->getThreadId());
       }
       return $ids;
     }

     public function addAccountToCron($Account, $threads){
        $ids = [];
       foreach($threads as $thread){
        $this->saveCronJob($Account, $thread);
       }
    }

    private function saveCronJob($Account, $thread){
      require_once PLUGINS_PATH."/".self::IDNAME."/models/CronJobModel.php";
      $CronJob = new CronJobModel;
      $messages = $this->getCurrentMessagesOrder($Account);
      $messages_count = count(json_decode($messages ,true));
      $CronJob->set("user_id", $Account->get('user_id'))
      ->set("account_id", $Account->get('id'))
      ->set("recipient_id", $thread->getInviter()->getPk())
      ->set("thread_id", $thread->getThreadId())
      ->set("fast_speed", true)
      ->set("inbox_count", $messages_count)
      ->set("messages", $messages)
      ->set("received_date", date('Y-m-d h:i:s', time()))
      ->save();
    }

    public function getCurrentMessagesOrder($Account) {
      $messages = [];
      $query = \DB::table('np_chatbot_messages')
      ->where("account_id", "=", $Account->get('id'))
      ->where("user_id", "=", $Account->get('user_id'))
      ->where("is_deleted", "=", false)
      ->select("*")
      ->orderBy("message_order","ASC")
      ->get();
      if(sizeOf($query)  > 0) {
        foreach($query as $q){
            $tmp = [ "id" => $q->id, "message" => $q->message ];
            array_push($messages, $tmp);
        }
      }
      return json_encode($messages);
    }


}