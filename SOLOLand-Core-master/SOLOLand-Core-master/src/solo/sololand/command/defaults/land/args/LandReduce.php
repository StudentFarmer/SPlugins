<?php

namespace solo\sololand\command\defaults\land\args;

use pocketmine\Server;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;

use solo\sololand\Main;
use solo\sololand\command\SubCommand;
use solo\sololand\world\World;
use solo\sololand\math\Square;
use solo\sololand\util\Queue;
use solo\solocore\util\Message;

class LandReduce extends SubCommand{

	public function __construct(){
		parent::__construct("축소", "땅 크기를 축소합니다.", ["줄이기"]);
		$this->setPermission("sololand.command.land.reduce");
		Server::getInstance()->getPluginManager()->registerEvents(new LandReduceListener(), Main::getInstance());
	}

	public function execute(CommandSender $sender, array $args){
		$world = World::getWorld($sender);
		$land = $world->getLandProvider()->getLand($sender);

		if(!$sender->hasPermission("sololand.administrate.land.ignoreDisallowResize") && !$world->getLandProperties()->isAllowResize()){
			Message::alert($sender, "해당 월드에서는 땅의 크기를 변경할 수 없습니다.");
			return true;
		}

		$queue = Queue::getQueue($sender);
		if($queue !== null && !$queue instanceof LandReduceQueue){
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
			$queue = new LandReduceQueue();
			$queue->set("land", $land);
			Queue::setQueue($sender, $queue);
			Message::normal($sender, "땅 축소를 시작합니다. 크기를 축소할 지점을 터치해주세요.");
			return true;
			
		}else switch($queue->getStep()){
			case 0:
				Message.normal(player, "이미 땅 축소가 진행중입니다. 크기를 축소할 지점을 터치해주세요.");
				return true;

			case 1: // final step
				$land = $world->getLandProvider()->getLandById($queue->get("land")->getId());
				if($land === null){
					Message::alert($sender, "땅이 존재하지 않습니다.");
					Queue::removeQueue($sender);
					return true;
				}
				if(!$sender->hasPermission("sololand.administrate.land.modify") && !$land->isOwner($sender)){
					Message::alert($sender, "땅 주인이 아니므로 땅 크기변경을 할 수 없습니다.");
					Queue::removeQueue($sender);
					return true;
				}
				
				$square = $queue->get("square");
				$canReduce = $land->getId() === $queue->get("land") && $land->isInside($square);
				if($canReduce){
					$reduced = $land->getSize() - $square->getSize();
					$land->set($square);
					Message::normal($sender, "성공적으로 땅 크기를 축소하였습니다. 축소된 크기 : " . $reduced . "블럭");
				}else{
					Message::alert($sender, "땅 축소를 진행하던 중 오류가 발생하였습니다. 잠시후 다시 시도해주세요.");
				}
				Queue::removeQueue($sender);
		}
		return true;
	}
}

class LandReduceQueue extends Queue{
	public function getName() : string{
		return "땅 축소";
	}
}

class LandReduceListener implements Listener{
	public function onInteract(PlayerInteractEvent $event){
		$player = $event->getPlayer();
		
		$queue = Queue::getQueue($player);
		if(!$queue instanceof LandReduceQueue){
			return;
			
		}

		$world = World::getWorld($player);
		if($queue->getStep() === 0){
			$land = $world->getLandProvider()->getLandById($queue->get("land")->getId());
			if($land === null){
				Queue::removeQueue($player);
				return;
			}
			if(!$land->isInside($event->getBlock())){
				Message::alert($player, "땅 안쪽을 터치하여야 땅 축소가 가능합니다.");
				return;
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
						Message::alert($player, "땅의 한변 길이는 " . $world->getLandProperties()->getMinLength() . " ~ " . $world->getLandProperties()->getMaxLength() . " 블럭 이어야 합니다. 땅 축소를 취소합니다.");
						Queue::removeQueue($player);
						return;
			}
			$queue->set("square", $squareReduced);
			$queue->setStep(1);
			Message::normal($player, "땅을 축소하려면 /땅 축소 명령어를 입력해주세요.");
		}
	}
}