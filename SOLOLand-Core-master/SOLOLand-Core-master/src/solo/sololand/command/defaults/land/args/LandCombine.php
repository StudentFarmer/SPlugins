<?php

namespace solo\sololand\command\defaults\land\args;

use pocketmine\command\CommandSender;
use solo\sololand\command\SubCommand;
use solo\sololand\world\World;
use solo\sololand\land\Land;
use solo\sololand\math\Square;
use solo\sololand\util\Queue;
use solo\solocore\util\Economy;
use solo\solocore\util\Message;

class LandCombine extends SubCommand{

	public function __construct(){
		parent::__construct("합치기", "다른 땅과 합쳐 크기를 확장합니다.", null, [
				["땅 번호"]
		]);
		$this->setPermission("sololand.command.land.combine");
	}

	public function execute(CommandSender $sender, array $args){
		$world = World::getWorld($sender);

		if(!$sender->hasPermission("sololand.administrate.land.ignoreDisallowCombine") && !$world->getLandProperties()->isAllowCombine()){
			Message::alert($sender, "해당 월드에서 땅을 합칠 수 없습니다.");
			return true;
		}
		
		$queue = Queue::getQueue($sender);
		$land = $world->getLandProvider()->getLand($sender);
		$squareCombined;
		
		if($queue !== null && !$queue instanceof LandCombineQueue){
			Message::alert($sender, $queue->getName() . " 작업이 진행중입니다. 해당 작업을 취소한 후 다시 시도해주세요.");
			return true;
				
		}else if($queue === null){ // first execute this command
			$land = $world->getLandProvider()->getLand($sender);
			if($land === null){
				Message::alert($sender, "현재 위치에서 땅을 찾을 수 없습니다.");
				return true;
			}
			if(!$sender->hasPermission("sololand.administrate.land.modify") && !$land->isOwner($sender)){
				Message::alert($sender, "땅 주인이 아니므로 땅을 합칠 수 없습니다.");
				return true;
			}
			if(!isset($args[0]) || !is_numeric($args[0])){
				return false;
			}
			$id = (int) $args[0];
			$targetLand = $world->getLandProvider()->getLandById($id);
			if($targetLand === null){
				Message::alert($sender, "해당 번호의 땅은 존재하지 않습니다.");
				return true;
			}
			if(!$sender->hasPermission("sololand.administrate.land.modify") && !$targetLand->isOwner($sender)){
				Message::alert($sender, $targetLand->getId() . "번 땅 주인이 아니므로 땅을 합칠 수 없습니다.");
				return true;
			}
			if($land->getId() === $targetLand->getId()){
				Message::alert($sender, "같은 땅끼리 서로 합칠 수 없습니다.");
				return true;
			}
			$squareCombined = new Square();
			$squareCombined->set($land);
			$squareCombined->expand($targetLand);
				
			Message::normal($sender, "합칠 시 땅 크기 : " . $squareCombined->getWidth() . "x" . $squareCombined->getDepth() . " (" . $squareCombined->getSize() . "블럭)");
			
			if(
					$world->getLandProperties()->getMinLength() > $squareCombined->getWidth()
					|| $world->getLandProperties()->getMinLength() > $squareCombined->getDepth()
					|| $world->getLandProperties()->getMaxLength() < $squareCombined->getWidth()
					|| $world->getLandProperties()->getMaxLength() < $squareCombined->getDepth()
					){
						Message::alert($player, "땅의 한변 길이는 " . $world->getLandProperties()->getMinLength() . " ~ " . $world->getLandProperties()->getMaxLength() . " 블럭 이어야 합니다. 땅 합치기를 취소합니다.");
						Queue::removeQueue($player);
						return true;
			}
		}else{
			$land = $world->getLandProvider()->getLandById($queue->get("land")->getId());
			$squareCombined = $queue->get("square");
			if(!$sender->hasPermission("sololand.administrate.land.modify") && !$land->isOwner($sender)){
				Message::alert($sender, "땅 주인이 아니므로 땅을 합칠 수 없습니다.");
				return true;
			}
		}
		
		$overlapList = $world->getLands(function (Land $land) use ($squareCombined) { return $squareCombined->isOverlap($land); });
		unset($overlapList[$land->getId()]);
		
		$cantCombine = [];
		$notOwned = [];
		$overlapRoomList = [];
		
		$overlapSize = 0;
		
		$temp = [];
		
		foreach($overlapList as $overlap){
			if(!$overlap->isOwner($sender)){
				$notOwned[] = $overlap->getId();
			}
			if(!$squareCombined->isInside($overlap)){
				$cantCombine[] = $overlap->getId();
			}
			if($overlap->hasRoom()){
				$overlapRoomList[$overlap->getId()] = $overlap->getRooms();
			}
			$overlapSize += $overlap->getSize();
			
			$temp[] = $overlap->getId() . "번";
		}
		Message::normal($sender, "총 " . count($overlapList) . "개의 겹치는 땅이 발견되었습니다 : " . implode(", ", $temp));
		
		$canCombine = true;
		$temp = [];
		
		// check if overlap lands are owned
		if(!$sender->hasPermission("sololand.administrate.land.modify") && count($notOwned) > 0){
			$canCombine = false;
			foreach($notOwned as $not){
				$temp[] = $not->getId() . "번";
			}
			Message::alert($sender, "겹치는 땅 중에 소유하지 않은 땅이 있어, 땅을 합칠 수 없습니다 : " . implode(", ", $temp));
		}
		
		$temp = [];
		
		// check if overlap lands are contained completly
		if(count($cantCombine) > 0){
			$canCombine = false;
			foreach($cantCombine as $not){
				$temp[] = $not->getId() . "번";
			}
			Message::alert($sender, "완전히 겹쳐져 있지 않은 땅은 합칠 수 없습니다 : " . implode(", ", $temp));
		}
		
		if(!$canCombine){
			return true;
		}
		
		$temp = [];
		
		if($queue === null && count($overlapRoomList) > 0){
			foreach($overlapRoomList as $landId => $rooms){
				foreach($rooms as $room){
					$temp[] = $landId . "-" . $room->getId() . "번";
				}
			}
			Message::normal($sender, "땅을 합칠 시 " . implode(", ", $temp) . " 방도 함께 합쳐집니다.");
		}
		
		if(!$sender->hasPermission("sololand.administrate.land.ignoreMoney")){
			$myMoney = Economy::getMoney($sender);
			$price = $world->getLandProperties()->getPricePerBlock() * ($squareCombined - $overlapSize - $land->getSize());
			Message::normal($sender, "땅을 합칠려면 " . $price . "원이 필요합니다.");
			if($myMoney < $price){
				Message::alert($sender, "돈이 부족하여 땅을 합칠 수 없습니다.");
				return true;
			}
			if($queue !== null){
				Economy::reduceMoney($sender, $price);
			}
		}
		
		if($queue === null){
			$queue = new LandCombineQueue();
			$queue->set("land", $land);
			$queue->set("square", $squareCombined);
			Queue::addQueue($sender, $queue);
			Message::normal($sender, "땅을 합치시려면 /땅 합치기 명령어를 한번 더 입력해주세요.");
			return true;
		}
		
		// add all rooms
		foreach($overlapRoomList as $room){
			$room->id = $land->getNextRoomId();
			$land->addRoom($room);
		}
		
		// remove overlap lands
		foreach($overlapList as $overlap){
			$world->getLandProvider()->removeLand($overlap->getId());
		}
		
		$beforeSize = $land->getSize();
		$land->set($squareCombined);
		Message::normal($sender, "성공적으로 땅를 합쳤습니다. 확장된 크기 : " . $squareCombined->getSize() - $beforeSize . "블럭, 합쳐진 땅 갯수 : " . count($overlapList) . (count($overlapRoomList) > 0 ? ", 합쳐진 방 갯수 : " . count($overlapRoomList) : ""));
		Queue::removeQueue($sender);
		return true;
	}
}

class LandCombineQueue extends Queue{
	public function getName() : string{
		return "땅 합치기";
	}
}