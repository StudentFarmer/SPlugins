<?php

namespace solo\sololand\handler\player;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use solo\sololand\world\World;

class PlayerDeathHandler implements Listener{
	
	public function handle(PlayerDeathEvent $event){
		if(World::getWorld($event->getPlayer())->getWorldProperties()->isInvensave()){
			$event->setKeepInventory(true);
		}
	}
}