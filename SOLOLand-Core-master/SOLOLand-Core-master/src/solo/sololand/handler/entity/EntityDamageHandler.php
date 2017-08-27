<?php

namespace solo\sololand\handler\entity;

use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use solo\sololand\world\World;

class EntityDamageHandler implements Listener{
	
	public function handle(EntityDamageEvent $event){
		if($event instanceof EntityDamageByEntityEvent && $event->getEntity() instanceof Player && $event->getDamager() instanceof Player){
			$world = World::getWorld($event->getEntity());
			$land = $world->getLandProvider()->getLand($event->getEntity());
			if($land !== null){
				if(!$land->isAllowPVP()){
					$event->setCancelled();
				}
				return;
			}
			if(!$world->getWorldProperties()->isAllowPVP()){
				$event->setCancelled();
			}
		}
	}
}