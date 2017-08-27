<?php

namespace solo\effectkeeper;

use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\entity\EntityEffectRemoveEvent;

class Main extends PluginBase implements Listener {

  public function onEnable() {
	$this->getServer()->getPluginManager()->registerEvents($this, $this);
  }

  public function onEffectRemove(EntityEffectRemoveEvent $event){
  	if($event->getEntity() instanceof Player && ! $event->getEntity()->isAlive()){
  		$event->setCancelled();
  	}
  }

  public function onRespawn(PlayerRespawnEvent $event){
  	$event->getPlayer()->sendPotionEffects($event->getPlayer());
  }
}