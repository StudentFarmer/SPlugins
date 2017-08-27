<?php

namespace solo\smotd;

class MotdChangeTask extends SMotdTask{

  public function _onRun(int $currentTick){
    if($currentTick % $this->owner->getChangeInterval() != 0){
      return;
    }
    $this->owner->next();
    $this->owner->getServer()->getNetwork()->setName(str_ireplace(
      [
        '{PLAYERS}',
        '{MAXPLAYERS}',
        '{TPS}',
        '{AVERAGETPS}'
      ],
      [
        count($this->owner->getServer()->getOnlinePlayers()),
        $this->owner->getServer()->getMaxPlayers(),
        $this->owner->getServer()->getTicksPerSecond(),
        $this->owner->getServer()->getTicksPerSecondAverage()
      ],
      $this->owner->getCurrentMotd()
    ));
  }
}
