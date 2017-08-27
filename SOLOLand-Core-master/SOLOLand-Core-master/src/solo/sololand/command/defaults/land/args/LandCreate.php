<?php

namespace solo\sololand\command\defaults\land\args;

use pocketmine\Server;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\command\CommandSender;
use pocketmine\math\Vector3;

use solo\sololand\Main;
use solo\sololand\command\SubCommand;
use solo\sololand\land\Land;
use solo\sololand\world\World;
use solo\sololand\math\Square;
use solo\sololand\util\Queue;
use solo\solocore\util\Message;
use solo\solocore\util\Economy;

class LandCreate extends SubCommand{

	public function __construct(){
		parent::__construct("생성", "땅을 생성합니다.");
		$this->setPermission("sololand.command.land.create");
		Server::getInstance()->getPluginManager()->registerEvents(new LandCreateListener(), Main::getInstance());
	}

	public function execute(CommandSender $sender, array $args){
		$world = World::getWorld($sender);

		if(!$sender->hasPermission("sololand.administrate.land.ignoreDisallowCreate") && !$world->isAllowCreateLand()){
			Message::alert($sender, "현재 월드에서 땅을 생성할 수 없습니다.");
			return true;
		}
		if(!$sender->hasPermission("sololand.administrate.land.ignoreMaxCount") && $world->getLandProperties()->getMaxCountPerPlayer() <= count($world->getLands(function(Land $land) use ($sender){ return $land->isOwner($sender); }))){
			Message::alert($sender, "해당 월드에서 소유할 수 있는 땅의 최대 갯수를 초과하였습니다. (최대 " . $world->getLandProperties()->getMaxCountPerPlayer() . "개)");
			return true;
		}
		
		$queue = Queue::getQueue($sender);
		if($queue === null){
			Queue::setQueue($sender, new LandCreateQueue());
			Message::normal($sender, "땅 생성을 시작합니다. 첫번째 지점을 터치해주세요.");
			
		}else if($queue instanceof LandCreateQueue){
			switch($queue->getStep()){
				case 0:
					Message::alert($sender, "이미 땅 생성이 진행중입니다. 블럭을 터치하여 첫번째 지점을 선택하여 주세요.");
					Message::alert($sender, "진행중인 작업을 취소하려면 /땅 취소 명령어를 입력하세요.");
					break;
	
				case 1:
					Message::alert($sender, "이미 땅 생성이 진행중입니다. 블럭을 터치하여 두번째 지점을 선택하여 주세요.");
					Message::alert($sender, "진행중인 작업을 취소하려면 /땅 취소 명령어를 입력하세요.");
					break;
	
				case 2:
					$square = $queue->get("square");
					$overlapList = $world->getLandProvider()->getLands(function(Land $land) use ($square){ return $square->isOverlap($land); });
					if(count($overlapList) > 0){
						$ids = [];
						foreach($overlapList as $overlap){
							$ids[] = $overlap->getId() . "번";
						}
						Message::alert($player, implode(", ", $ids) . " 땅과 겹칩니다. 땅 생성을 취소합니다.");
						Queue::removeQueue($player);
						break;
					}
					if(!$sender->hasPermission("sololand.administrate.land.ignoreMoney")){
						$price = $world->getLandProperties()->getPricePerBlock() * $square->getSize();
						$myMoney = Economy::getMoney($sender);
						if($myMoney < $price){
							Message::alert($sender, "돈이 부족합니다.");
							Queue::removeQueue($sender);
							break;
						}
						Economy::reduceMoney($sender, $price);
					}
					$land = new Land($world->getLandProvider()->getNextLandId(), $square);
					$land->setOwner($sender); // set owner;
					$centerX = $land->getStartX() + ($land->getWidth() / 2);
					$centerZ = $land->getStartZ() + ($land->getDepth() / 2);
					$land->setSpawnPoint(new Vector3($centerX, $world->getLevel()->getHighestBlockAt($centerX, $centerZ) + 1, $centerZ)); // set spawnpoint
					
					$world->getLandProvider()->addLand($land);
					Message::normal($sender, "성공적으로 땅을 생성하였습니다. 땅 번호는 " . $land->getId() . "번 입니다.");
					Queue::removeQueue($sender);
					break;
			}
			
		}else{
			Message::alert($sender, $queue->getName() . " 작업이 진행중입니다. 해당 작업을 취소한 후 다시 시도해주세요.");
		}
		return true;
	}
}

class LandCreateQueue extends Queue{
	public function getName() : string{
		return "땅 생성";
	}
}

class LandCreateListener implements Listener{
	
	public function onInteract(PlayerInteractEvent $event){
		$player = $event->getPlayer();
		
		$queue = Queue::getQueue($player);
		if(!$queue instanceof LandCreateQueue){
			return;
		}

		$world = World::getWorld($player);
		switch($queue->getStep()){
			case 0:
				Message::normal($player, "첫번째 지점을 선택하였습니다. 두번째 지점을 선택해주세요.");
				$queue->set("position", $event->getBlock());
				$queue->setStep(1);
				break;
				
			case 1:
				$square = Square::create($queue->get("position"), $event->getBlock());
				Message::normal($player, "두번째 지점을 선택하였습니다.");
				
				$overlapList = $world->getLandProvider()->getLands(function(Land $land) use ($square){ return $square->isOverlap($land); });
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
				if(!$player->hasPermission("sololand.administrate.land.ignoreMoney")){
					$price = $world->getLandProperties()->getPricePerBlock() * $square->getSize();
					$myMoney = Economy::getMoney($sender);
					Message::normal($player, "땅 생성 비용은 " . $price . "원 입니다.");
					if($myMoney < $price) {
						Message::alert($player, "돈이 부족합니다.");
						Queue::removeQueue($player);
						break;
					}
				}
				$queue->remove("position");
				$queue->set("square", $square);
				$queue->setStep(2);
				Message::normal($player, "땅을 생성하려면 /땅 생성 명령어를 입력해주세요.");
		}
	}
}