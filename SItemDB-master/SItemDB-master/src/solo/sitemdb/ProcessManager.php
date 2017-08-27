<?php

namespace solo\sitemdb;

use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerQuitEvent;

class ProcessManager implements Listener{

  private $owner;

  private $processList = [];

  public function __construct(SItemDB $owner){
    $this->owner = $owner;

    $this->owner->getServer()->getPluginManager()->registerEvents($this, $this->owner);
  }

  public function setProcess(Player $player, Process $process){
    $this->processList[$player->getName()] = $process;
  }

  public function getProcess(Player $player){
    return $this->processList[$player->getName()] ?? null;
  }

  public function removeProcess(Player $player){
    unset($this->processList[$player->getName()]);
  }

  public function handlePlayerQuit(PlayerQuitEvent $event){
    $this->removeProcess($event->getPlayer());
  }

  public function handlePlayerInteract(PlayerInteractEvent $event){
    if(($process = $this->getProcess($event->getPlayer())) !== null){
      $process->handlePlayerInteract($event);
    }
  }

  public function handlePlayerItemHeld(PlayerItemHeldEvent $event){
    if(($process = $this->getProcess($event->getPlayer())) !== null){
      $process->handlePlayerItemHeld($event);
    }
  }
}
