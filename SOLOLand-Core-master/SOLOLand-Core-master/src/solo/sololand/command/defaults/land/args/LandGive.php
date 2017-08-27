<?php

namespace solo\sololand\command\defaults\land\args;

use pocketmine\Server;
use pocketmine\command\CommandSender;
use solo\sololand\command\SubCommand;
use solo\sololand\world\World;
use solo\sololand\land\Land;
use solo\sololand\util\Queue;
use solo\solocore\util\Message;
use solo\solonotification\Notification;

class LandGive extends SubCommand{

	public function __construct(){
		parent::__construct("양도", "다른 유저에게 땅을 양도합니다.", ["주기", "넘기기"], [
				["유저"]
		]);
		$this->setPermission("sololand.command.land.give");
	}

	public function execute(CommandSender $sender, array $args){
		$world = World::getWorld($sender);
		$land = $world->getLandProvider()->getLand($sender);
		if($land === null){
			Message::alert($sender, "현재 위치에서 땅을 찾을 수 없습니다.");
			return true;
		}
		if(!$sender->hasPermission("sololand.administrate.land.modify") && !$land->isOwner($sender)){
			Message::alert($sender, "땅 주인이 아니므로 땅을 다른 유저에게 양도할 수 없습니다.");
			return true;
		}
		
		$queue = Queue::getQueue($sender);
		if($queue === null){
			if(!isset($args[0])){
				return false;
			}
			$target = Server::getInstance()->getPlayer($args[0]);
			if($target === null){
				Message::alert($sender, $args[0] . "님은 현재 온라인이 아닙니다.");
				return true;
			}
			if($land->isOwner($target)){
				Message::alert($sender, "땅 주인에게 땅을 줄 수 없습니다.");
				return true;
			}
			if($world->getLandProperties()->getMaxCountPerPlayer() <= count($world->getLands(function (Land $land) use ($target) { return $land->isOwner($target); } ))){
				Message::alert($sender, $target->getName() . "님이 해당 월드에서 소유할 수 있는 땅의 최대 갯수를 초과하였습니다. (최대 " . $world->getLandProperties()->getMaxCountPerPlayer() . "개)");
				return true;
			}
			
			$queue = new LandGiveQueue();
			$queue->set("player", $target->getName());
			Queue::setQueue($sender, $queue);
			Message::normal($sender, "땅을 정말로 " . $target->getName() . "님께 양도하시겠습니까? 양도하시려면 /땅 양도 명령어를 한번 더 입력해주세요.");
			return true;
				
		}else if($queue instanceof LandGiveQueue){
			$targetName = $queue->get("player");
			if($world->getLandProperties()->getMaxCountPerPlayer() <= count($world->getLands(function (Land $land) use ($targetName) { return $land->isOwner($targetName); } ))){
				Message::alert($sender, $targetName . "님이 해당 월드에서 소유할 수 있는 땅의 최대 갯수를 초과하였습니다. (최대 " . $world->getLandProperties()->getMaxCountPerPlayer() . "개)");
				return true;
			}
			$land->clear();
			$land->setOwner($targetName);
			Message::normal($sender, $targetName . "님에게 " . $land->getId() . "번 땅을 양도 처리 하였습니다.");
			
			@Notification::addNotification($targetName, $sender->getName() . "님이 " . $world->getName() . " 월드의 " . $land->getId() . "번 땅을 양도하셨습니다.");
			
		}else{
			Message::alert($sender, $queue->getName() . " 작업이 진행중입니다. 해당 작업을 취소한 후 다시 시도해주세요.");
		}
		return true;
	}
}

class LandGiveQueue extends Queue{
	public function getName() : string{
		return "땅 양도";
	}
}