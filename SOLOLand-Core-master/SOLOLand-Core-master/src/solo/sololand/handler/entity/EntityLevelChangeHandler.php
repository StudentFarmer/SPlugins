<?php

namespace solo\sololand\handler\entity;

use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\event\entity\EntityLevelChangeEvent;
use solo\sololand\util\Queue;
use solo\solocore\util\Message;

class EntityLevelChangeHandler implements Listener{
	
	public function handle(EntityLevelChangeEvent $event){
		if($event->getEntity() instanceof Player){
			$queue = Queue::getQueue($event->getEntity());
			if($queue !== null){
				Queue::removeQueue($event->getEntity());
				Message::alert($event->getEntity(), "월드가 변경되어, 진행중이던 " . $queue->getName() . " 작업이 취소되었습니다.");
			}
		}
	}
	
}