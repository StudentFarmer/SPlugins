<?php

namespace solo\sololand\handler\level;

use pocketmine\event\Listener;
use pocketmine\event\level\LevelLoadEvent;
use pocketmine\level\Level;
use solo\sololand\world\World;

class LevelLoadHandler implements Listener{
	
	public function handle(LevelLoadEvent $event){
		World::loadWorld($event->getLevel());
	}
}