<?php

namespace solo\sololand\handler\player;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;

use solo\sololand\land\Land;
use solo\sololand\land\Room;
use solo\sololand\world\World;
use solo\sololand\math\Square;
use solo\sololand\math\Cuboid;
use solo\sololand\util\Queue;
use solo\sololand\util\QueueType;
use solo\solocore\util\Message;
use solo\solocore\util\Economy;

class PlayerInteractHandler implements Listener{
	
/*	public function handle(PlayerInteractEvent $event){
		if($event->getAction() !== PlayerInteractEvent::RIGHT_CLICK_BLOCK){
			return;
		}
		$player = $event->getPlayer();
		
		$queue = Queue::getQueue($player);
		if($queue === null){
			return;
		}
		
		$world = World::getWorld($player);
		$land = $world->getLandProvider()->getLand($player);
		$myMoney = Economy::getMoney($player);
		
		switch($queue->getType()){
			case QueueType::LAND_CREATE_FIRST:
				Message::normal($player, "첫번째 지점을 선택하였습니다. 두번째 지점을 선택해주세요.");
				$queue->set("position", $event->getBlock());
				$queue->setType(QueueType::LAND_CREATE_SECOND);
				break;
		
			case QueueType::LAND_CREATE_SECOND:
				$square = Square::create($queue->get("position"), $event->getBlock());
				Message::normal($player, "두번째 지점을 선택하였습니다.");
		
				$overlapList = $world->getLandProvider()->getLands(function(Land $land) use ($square){
					return $square->isOverlap($land);
				});
				if(count($overlapList) > 0){
					$ids = [];
					foreach($overlapList as $overlap){
						$ids[] = $overlap->getId() . "번";
					}
					Message::alert($player, implode(", ", $ids) . " 땅과 겹칩니다. 땅 생성을 취소합니다.");
					Queue::removeQueue($player);
					break;
				}
				Message::normal($player, "땅 크기 : " . $square->getWidth() . "x" . $square->getDepth() . " (" . $square->getSize() . " 블럭)");
				if(
						$world->getLandProperties()->getMinLength() > $square->getWidth()
						|| $world->getLandProperties()->getMinLength() > $square->getDepth()
						|| $world->getLandProperties()->getMaxLength() < $square->getWidth()
						|| $world->getLandProperties()->getMaxLength() < $square->getDepth()
						){
							Message::alert($player, "땅의 한변 길이는 " . $world->getLandProperties()->getMinLength() . " ~ " . $world->getLandProperties()->getMaxLength() . " 블럭 이어야 합니다. 땅 생성을 취소합니다.");
							Queue::removeQueue($player);
							break;
				}
				if(!$player->isOp()){
					$price = $world->getLandProperties()->getPricePerBlock() * $square->getSize();
					Message::normal($player, "땅 생성 비용은 " . $price . "원 입니다.");
					if($myMoney < $price) {
						Message::alert($player, "돈이 부족합니다.");
						Queue::removeQueue($player);
						break;
					}
				}
				$queue->remove("position");
				$queue->set("square", $square);
				$queue->setType(QueueType::LAND_CREATE_THIRD);
				Message::normal($player, "땅을 생성하려면 /땅 생성 명령어를 입력해주세요.");
				break;
		
			case QueueType::LAND_EXPAND_FIRST:
				$id = $queue->get("id");
				$land = $world->getLandProvider()->getLandById($id);
				if($land->isInside($event->getBlock())){
					Message::alert($player, "땅 바깥을 터치하여야 땅 확장이 가능합니다. 땅 확장을 취소합니다.");
					Queue::removeQueue($player);
					break;
				}
				$squareExpanded = new Square();
				$squareExpanded->set($land);
				$squareExpanded->expand($event->getBlock());
		
				$overlapList = $world->getLandProvider()->getLands(function(Land $land) use ($square){
					return $square->isOverlap($land);
				});
				if(count($overlapList) > 0){
					$ids = [];
					foreach($overlapList as $overlap){
						$ids[] = $overlap->getId() . "번";
					}
					Message::alert($player, implode(", ", $ids) . " 땅과 겹칩니다. 땅 생성을 취소합니다.");
					Queue::removeQueue($player);
					break;
				}
				Message::normal($player, "확장시 땅 크기 : " . $squareExpanded->getWidth() . "x" . $squareExpanded->getDepth() . " (" . $squareExpanded->getSize() . " 블럭)");
				if(
						$world->getLandProperties()->getMinLength() > $square->getWidth()
						|| $world->getLandProperties()->getMinLength() > $square->getDepth()
						|| $world->getLandProperties()->getMaxLength() < $square->getWidth()
						|| $world->getLandProperties()->getMaxLength() < $square->getDepth()
						){
							Message::alert($player, "땅의 한변 길이는 " . $world->getLandProperties()->getMinLength() . " ~ " . $world->getLandProperties()->getMaxLength() . " 블럭 이어야 합니다. 땅 생성을 취소합니다.");
							Queue::removeQueue($player);
							break;
				}
		

				if(!$player->isOp()){
					$price = $world->getLandProperties()->getPricePerBlock() * ($square->getSize() - $land->getSize());
					Message::normal($player, "땅 확장 비용은 " . $price . "원 입니다.");
					if($myMoney < $price) {
						Message::alert($player, "돈이 부족합니다.");
						Queue::removeQueue($player);
						break;
					}
				}
				$queue->set("square", $squareExpanded);
				$queue->setType(QueueType::LAND_EXPAND_SECOND);
				Message::normal($player, "땅을 확장하려면 /땅 확장 명령어를 입력해주세요.");
				break;
		
			case QueueType::LAND_REDUCE_FIRST:
				$id = $queue->get("id");
				$land = $world->getLandProvider()->getLandById($id);
				if(!$land->isInside($event->getBlock())){
					Message::alert($player, "땅 안쪽을 터치하여야 땅 축소가 가능합니다. 땅 축소를 취소합니다.");
					Queu::removeQueue($player);
					break;
				}
				$squareReduced = new Square();
				$squareReduced->set($land);
				$squareReduced->reduce($event->getBlock());

				Message::normal($player, "축소시 땅 크기 : " . $squareReduced->getWidth() . "x" . $squareReduced->getDepth() . " (" . $squareReduced->getSize() . " 블럭)");
				if(
						$world->getLandProperties()->getMinLength() > $square->getWidth()
						|| $world->getLandProperties()->getMinLength() > $square->getDepth()
						|| $world->getLandProperties()->getMaxLength() < $square->getWidth()
						|| $world->getLandProperties()->getMaxLength() < $square->getDepth()
						){
							Message::alert($player, "땅의 한변 길이는 " . $world->getLandProperties()->getMinLength() . " ~ " . $world->getLandProperties()->getMaxLength() . " 블럭 이어야 합니다. 땅 생성을 취소합니다.");
							Queue::removeQueue($player);
							break;
				}
				$queue->set("square", $squareReduced);
				$queue->setType(QueueType::LAND_REDUCE_SECOND);
				Message::normal($player, "땅을 축소하려면 /땅 축소 명령어를 입력해주세요.");
				break;
		
			case QueueType::ROOM_CREATE_FIRST:
				$id = $queue->get("id");
				$land = $world->getLandProvider()->getLandById($id);
				if(!$land->isInside($event->getBlock())){
					Message::alert($player, "땅 안쪽을 터치해주세요.");
					break;
				}
				$queue->set("position", $event->getBlock());
				$queue->setType(QueueType::ROOM_CREATE_SECOND);
				Message::normal($player, "첫번째 지점을 선택하였습니다. 두번째 지점을 선택해주세요.");
				break;
		
			case QueueType::ROOM_CREATE_SECOND:
				$id = $queue->get("id");
				$land = $world->getLandProvider()->getLandById($id);
				if(!$land->isInside($event->getBlock())){
					Message::normal($player, "땅 안쪽을 터치해주세요.");
					break;
				}
				$cuboid = Cuboid::create($queue->get("position"), $event->getBlock());
				Message::normal($player, "두번째 지점을 선택하였습니다.");
		
				$overlapList = $land->getRooms(function(Room $room) use ($cuboid){
					return $cuboid->isOverlap($room);
				});
				if(count($overlapList) > 0){
					$ids = [];
					foreach($overlapList as $overlap){
						$ids[] = $overlap . "번";
					}
					Message::alert($player, implode(", ", $ids) . " 방과 겹칩니다. 방 생성을 취소합니다.");
					Queue::removeQueue($player);
					break;
				}
				Message::normal($player, "방 크기 : " . $cuboid->getWidth() . "x" . $cuboid>getDepth() . ", 높이 : " . $cuboid->getHeight() . " (" . $cuboid->getSize() . " 블럭)");
				if(
						$world->getRoomProperties()->getMinLength() > $cuboid->getWidth()
						|| $world->getRoomProperties()->getMinLength() > $cuboid->getDepth()
						|| $world->getRoomProperties()->getMinLength() > $cuboid->getHeight()
						|| $world->getRoomProperties()->getMaxLength() < $cuboid->getWidth()
						|| $world->getRoomProperties()->getMaxLength() < $cuboid->getDepth()
						|| $world->getRoomProperties()->getMaxLength() < $cuboid->getHeight()
						){
							Message::alert($player, "방의 한변 길이는 " . $world->getRoomProperties()->getMinLength() . " ~ " . $world->getRoomProperties()->getMaxLength() . " 블럭 이어야 합니다. 방 생성을 취소합니다.");
							Queue::removeQueue($player);
							break;
				}

				if(!$player->isOp()){
					$price = $world->getRoomProperties()->getPricePerBlock() * $cuboid->getSize();
					Message::normal($player, "방 생성 비용은 " . $price . "원 입니다.");
					if($myMoney < $price) {
						Message::alert($player, "돈이 부족합니다.");
						Queue::removeQueue($player);
						break;
					}
				}
				$queue->set("cuboid", $cuboid);
				$queue->setType(QueueType::ROOM_CREATE_THIRD);
				Message::normal($player, "방을 생성하려면 /방 생성 명령어를 다시 입력해주세요.");
				break;
				
			default:
				Debug::alert(Main::getInstance(), "Unhandled queue given : " . $queue->getType());
		}
		$event->setCancelled();
	}*/
}