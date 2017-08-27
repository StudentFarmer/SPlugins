<?php

namespace solo\sannounce\task;

use solo\sannounce\SAnnounceTask;

class AnnounceTask extends SAnnounceTask{

  public function _onRun(int $currentTick){
    $announce = $this->owner->getNextAnnounce();
    if($announce !== null){
      $prefix = $this->owner->getAnnouncePrefix();
      if($prefix !== "" && substr($prefix, -1) !== " "){
        $prefix .= " ";
      }
      $this->owner->getServer()->broadcastMessage($prefix . $announce);
    }
  }
}
