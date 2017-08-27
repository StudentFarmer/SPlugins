<?php

namespace solo\sololand\handler\inventory;

use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\inventory\InventoryPickupItemEvent;
use solo\sololand\world\World;
use solo\solocore\util\Message;

class InventoryPickupItemHandler implements Listener{
	
	public $lastPick = [];
	
	public function handleQuit(PlayerQuitEvent $event){
		unset($this->lastPick[$event->getPlayer()->getName()]);
	}
	
	public function handle(InventoryPickupItemEvent $event){
		if(!$event->getInventory()->getHolder() instanceof Player){
			return;
		}
		$player = $event->getInventory()->getHolder();
		$name = $player->getName();
		if(isset($this->lastPick[$name]) && $this->lastPick[$name] + 2 > time()){
			$event->setCancelled();
			return;
		}
		$world = World::getWorld($player);
		$land = $world->getLandProvider()->getLand($event->getItem());
		if($land === null){
			return;
		}
		if($land->isAllowPickupItem()){
			return;
		}
		if($land->isOwner($player) || $land->isMember($player)){
			return;
		}
		$room = $land->getRoom($event->getItem());
		if($room !== null && ($room->isOwner($player) || $room->isMember($player))){
			return;
		}
		$this->lastPick[$name] = time();
		$event->setCancelled();
		Message::alert($player, "아이템을 주울 수 없습니다", Message::TYPE_POPUP);
	}
}