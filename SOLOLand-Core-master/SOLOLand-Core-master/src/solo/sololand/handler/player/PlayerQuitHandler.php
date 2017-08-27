<?php

namespace solo\sololand\handler\player;

use pocketmine\event\Listener;
use pocketmine\event\Player\PlayerQuitEvent;
use solo\sololand\util\Queue;

class PlayerQuitHandler implements Listener{
	
	public function handle(PlayerQuitEvent $event){
		Queue::removeQueue($event->getPlayer());
	}
}