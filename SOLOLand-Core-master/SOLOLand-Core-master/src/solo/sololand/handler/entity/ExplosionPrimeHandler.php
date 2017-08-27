<?php

namespace solo\sololand\handler\entity;

use pocketmine\event\Listener;
use pocketmine\event\entity\ExplosionPrimeEvent;
use solo\sololand\world\World;

class ExplosionPrimeHandler implements Listener{
	
	public function handle(ExplosionPrimeEvent $event){
		if(!World::getWorld($event->getEntity()->getLevel())->getWorldProperties()->isAllowExplosion()){
			$event->setBlockBreaking(false);
		}
	}
}