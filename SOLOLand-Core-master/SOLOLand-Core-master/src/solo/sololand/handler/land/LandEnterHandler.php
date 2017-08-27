<?php

namespace solo\sololand\handler\land;

use pocketmine\event\Listener;
use solo\sololand\event\land\LandEnterEvent;
use solo\solocore\util\Message;

class LandEnterHandler implements Listener{
	
	public function handle(LandEnterEvent $event){
		$land = $event->getLand();
		if(
			!$land->isAllowAccess() &&
			!$event->getPlayer()->hasPermission("sololand.administrate.land.access") &&
			!$land->isOwner($event->getPlayer()) &&
			!$land->isMember($event->getPlayer())
		){
			$canEnter = false;
			foreach($land->getRooms() as $room){
				if(
					$room->isOwner($event->getPlayer())
					|| $room->isMember($event->getPlayer())
				){
					$canEnter = true;
				}
			}
			if(!$canEnter){
				Message::alert($event->getPlayer(), "출입이 허용되지 않은 땅입니다", Message::TYPE_POPUP);
				$event->setCancelled();
			}
		}
	}
}