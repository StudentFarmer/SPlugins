<?php

namespace solo\illegalmovefix;

use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\cheat\PlayerIllegalMoveEvent;

class Main extends PluginBase implements Listener{

  public function onEnable() {
	$this->getServer()->getPluginManager()->registerEvents($this, $this);
  }

  public function onIllegalMove(PlayerIllegalMoveEvent $event){
  	$event->setCancelled();
  }
}