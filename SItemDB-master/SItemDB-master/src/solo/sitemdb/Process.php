<?php

namespace solo\sitemdb;

use pocketmine\Player;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemHeldEvent;

abstract class Process{

  public function __construct(Player $player){
    $this->player = $player;
  }

  abstract public function getName() : string;

  public function handlePlayerInteract(PlayerInteractEvent $event){

  }

  public function handlePlayerItemHeld(PlayerItemHeldEvent $event){

  }
}
