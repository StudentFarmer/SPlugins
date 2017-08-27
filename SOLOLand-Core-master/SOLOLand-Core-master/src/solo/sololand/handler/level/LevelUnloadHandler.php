<?php

namespace solo\sololand\handler\level;

use pocketmine\event\Listener;
use pocketmine\event\level\LevelUnloadEvent;
use solo\sololand\world\World;

class LevelUnloadHandler implements Listener{
	
	public function handle(LevelUnloadEvent $event){
		$world = World::getWorld($event->getLevel());
		World::unloadWorld($world);
	}
}