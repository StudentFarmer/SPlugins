<?php

namespace solo\sololand\command\defaults\land\args;

use pocketmine\Server;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\command\CommandSender;

use solo\sololand\Main;
use solo\sololand\command\SubCommand;
use solo\sololand\world\World;
use solo\sololand\land\Land;
use solo\sololand\util\Queue;
use solo\solocore\util\Message;
use solo\solocore\util\Economy;

class LandExpand extends SubCommand{

	public function __construct(){
		parent::__construct("확장", "땅 크기를 확장합니다.", ["늘리기"]);
		$this->setPermission("sololand.command.land.expand");
		Server::getInstance()->getPluginManager()->registerEvents(new LandExpandListener(), Main::getInstance());
	}

	public function execute(CommandSender $sender, array $args){
		$world = World::getWorld($sender);

		if(!$sender->hasPermission("sololand.administrate.land.ignoreDisallowResize") && !$world->getLandProperties()->isAllowResize()){
			Message::alert($sender, "해당 월드에서 땅의 크기를 변경할 수 없습니다.");
			return true;
		}
		
		$land = $world->getLandProvider()->getLand($sender);
		
		$queue = Queue::getQueue($sender);
		if($queue !== null && !$queue instanceof LandExpandQueue){
			Message::alert($sender, $queue->getName() . " 작업이 진행중입니다. 해당 작업을 취소한 후 다시 시도해주세요.");
			return true;
				
		}else if($queue === null){
			if($land === null){
				Message::alert($sender, "현재 위치에서 땅을 찾을 수 없습니다.");
				return true;
			}
			if(!$sender->hasPermission("sololand.administrate.land.modify") && !$land->isOwner($sender)){
				Message::alert($sender, "땅 주인이 아니므로 땅의 크기를 변경할 수 없습니다.");
				return true;
			}
			$queue = new LandExpandQueue();
			$queue->set("land", $land);
			Queue::setQueue($sender, $queue);
			Message::normal($sender, "땅 확장을 시작합니다. 크기를 확장할 지점을 터치해주세요.");
			return true;
	
		}else switch($queue->getStep()){
			case 0:
				Message::normal(player, "이미 땅 확장이 진행중입니다. 크기를 확장할 지점을 터치해주세요.");
				return true;
				
			case 1:
				$land = $world->getLandProvider()->getLandById($queue->get("land")->getId());
				if($land === null){
					Message::alert($sender, "땅이 존재하지 않습니다.");
					Queue::removeQueue($sender);
					return true;
				}
				if(!$sender->hasPermission("sololand.administrate.land.modify") && !$land->isOwner($sender)){
					Message::alert($sender, "땅 주인이 아니므로 땅의 크기를 변경할 수 없습니다.");
					Queue::removeQueue($sender);
					return true;
				}
				$square = $queue->get("square");
				$overlapList = $world->getLandProvider()->getLands(function (Land $check) use ($square) { return $square->isOverlap($check); } );
				unset($overlapList[$land->getId()]);
				if(count($overlapList) > 0){
					$ids = [];
					foreach($overlapList as $overlap){
						$ids[] = $overlap->getId() . "번";
					}
					Queue::removeQueue($sender);
					Message::alert($sender, implode($ids) . " 땅과 겹칩니다. 땅 확장을 취소합니다.");
					return true;
				}
				$expanded = $square->getSize() - $land->getSize();
				if(!$sender->hasPermission("sololand.administrate.land.ignoreMoney")){
					$myMoney = Economy::getMoney($sender);
					$price = $world->getLandProperties()->getPricePerBlock() * $expanded;
					if($myMoney < $price){
						Message::alert($sender, "돈이 부족합니다.");
						Queue::removeQueue($sender);
						return true;
					}
					Economy::reduceMoney($sender, $price);
				}
				$land->set($square);
				Queue::removeQueue($sender);
				Message::normal($sender, "성공적으로 땅를 확장하였습니다. 확장된 크기 : " . $expanded . "블럭");
		}
		return true;
	}
}

class LandExpandQueue extends Queue{
	public function getName() : string{
		return "땅 확장";
	}
}

class LandExpandListener implements Listener{
	
	public function onInteract(PlayerInteractEvent $event){
		$player = $event->getPlayer();
		
		$queue = Queue::getQueue($player);
		if(!$queue instanceof LandExpandQueue){
			return;
		}
		
		$world = World::getWorld($player);
		if($queue->getStep() === 0){
			$id = $queue->get("id");
			$land = $world->getLandProvider()->getLandById($id);
			if($land === null){
				Queue::removeQueue($player);
				return;
			}
			if($land->isInside($event->getBlock())){
				Message::alert($player, "땅 바깥을 터치하여야 땅 확장이 가능합니다.");
				return;
			}
			$squareExpanded = new Square();
			$squareExpanded->set($land);
			$squareExpanded->expand($event->getBlock());
				
			$overlapList = $world->getLandProvider()->getLands(function(Land $land) use ($squareExpanded){ return $squareExpanded->isOverlap($land); });
			if(count($overlapList) > 0){
				$ids = [];
				foreach($overlapList as $overlap){
					$ids[] = $overlap->getId() . "번";
				}
				Message::alert($player, implode(", ", $ids) . " 땅과 겹칩니다. 땅 확장을 취소합니다.");
				Queue::removeQueue($player);
				return;
			}
			
			Message::normal($player, "확장시 땅 크기 : " . $squareExpanded->getWidth() . "x" . $squareExpanded->getDepth() . " (" . $squareExpanded->getSize() . " 블럭)");
			if(
					$world->getLandProperties()->getMinLength() > $square->getWidth()
					|| $world->getLandProperties()->getMinLength() > $square->getDepth()
					|| $world->getLandProperties()->getMaxLength() < $square->getWidth()
					|| $world->getLandProperties()->getMaxLength() < $square->getDepth()
					){
						Message::alert($player, "땅의 한변 길이는 " . $world->getLandProperties()->getMinLength() . " ~ " . $world->getLandProperties()->getMaxLength() . " 블럭 이어야 합니다. 땅 확장을 취소합니다.");
						Queue::removeQueue($player);
						return;
			}
					
			if(!$player->hasPermission("sololand.administrate.land.ignoreMoney")){
				$price = $world->getLandProperties()->getPricePerBlock() * ($squareExpanded->getSize() - $land->getSize());
				Message::normal($player, "땅 확장 비용은 " . $price . "원 입니다.");
				if($myMoney < $price) {
					Message::alert($player, "돈이 부족합니다.");
					Queue::removeQueue($player);
					return;
				}
			}
			$queue->set("square", $squareExpanded);
			$queue->setStep(1);
			Message::normal($player, "땅을 확장하려면 /땅 확장 명령어를 입력해주세요.");
		}
	}
}