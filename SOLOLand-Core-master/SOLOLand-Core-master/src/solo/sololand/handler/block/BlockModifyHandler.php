<?php

namespace solo\sololand\handler\block;

use pocketmine\event\Listener;
use solo\sololand\world\World;
use solo\solocore\event\block\BlockModifyEvent;
use solo\solocore\util\Message;

class BlockModifyHandler implements Listener{
	
	public function handle(BlockModifyEvent $event){	
		$player = $event->getPlayer();
		$block = $event->getBlock();
		
		$world = World::getWorld($block);
		$land = $world->getLandProvider()->getLand($block);
		
		if($land !== null){
			if($player->hasPermission("sololand.administrate.land.modify")){
				return; // if player has this permission, can modify land and room
			}
			
			if($land->hasRoom()){
				$room = $land->getRoom($block);
				if($room !== null){
					if(!$land->isOwner($player) && !$room->isOwner($player) && !$room->isMember($player)){
						Message::alert($player, "이 방을 수정할 수 없습니다", Message::TYPE_POPUP);
						$event->setCancelled();
					}else if($room->isSail()){
						Message::alert($player, "방이 매물에 등록되어 있는 동안엔 방을 수정할 수 없습니다", Message::TYPE_POPUP);
						$event->setCancelled();
					}else if($land->isSail()){
						Message::alert($player, "땅이 매물에 등록되어 있는 동안엔 땅을 수정할 수 없습니다.", Message::TYPE_POPUP);
						$event->setCancelled();
					}
					return;
				}
			}
			
			if(!$land->isOwner($player) && !$land->isMember($player)){
				Message::alert($player, "이 땅을 수정할 수 없습니다", Message::TYPE_POPUP);
				$event->setCancelled();
			}else if($land->isSail()){
				Message::alert($player, "땅이 매물에 등록되어 있는 동안엔 땅을 수정할 수 없습니다", Message::TYPE_POPUP);
				$event->setCancelled();
			}
			return;
		}
		if(!$player->hasPermission("sololand.administrate.world.modify") && $world->getWorldProperties()->isProtected()){
			$event->setCancelled();
		}
	}
}