<?php

namespace solo\sololand\command\defaults\land\args;

use pocketmine\command\CommandSender;
use solo\sololand\command\SubCommand;
use solo\sololand\world\World;
use solo\sololand\util\Queue;
use solo\solocore\util\Message;

class LandRemove extends SubCommand{
	
	public function __construct(){
		parent::__construct("삭제", "땅을 삭제합니다.", ["제거"]);
		$this->setPermission("sololand.command.land.remove");
	}

	public function execute(CommandSender $sender, array $args){
		$world = World::getWorld($sender);
		$land = $world->getLandProvider()->getLand($sender);
		
		if($land === null){
			Message::normal($sender, "현재 위치에서 땅을 찾을 수 없습니다.");
			return true;
		}
		
		if(!$sender->hasPermission("sololand.administrate.land.remove") && !$land->isOwner($sender)){
			Message::alert($sender, "땅 주인이 아니므로 땅을 삭제할 수 없습니다.");
			return false;
		}

		$queue = Queue::getQueue($sender);
		if($queue === null){
			$queue = new LandRemoveQueue();
			$queue->set("id", $land->getId());
			Queue::setQueue($sender, $queue);
			Message::normal($sender, "정말로 땅을 삭제하시겠습니까? 삭제하려면 /땅 삭제 명령어를 한번 더 입력해주세요.");
			
		}else if($queue instanceof LandRemoveQueue){
			if($land->getId() === $queue->get("id") && $world->getLandProvider()->removeLand($land->getId())){
				Message::normal($sender, "성공적으로 " . $land->getId() . "번 땅을 삭제하였습니다.");
			}else{
				Message::alert($sender, "땅 삭제를 진행하던 중 오류가 발생하였습니다. 잠시후 다시 시도해주세요.");
			}
			Queue::removeQueue($sender);
			
		}else{
			Message::alert($sender, $queue->getName() . " 작업이 진행중입니다. 해당 작업을 취소한 후 다시 시도해주세요.");
		}
		return true;
	}
}

class LandRemoveQueue extends Queue{
	public function getName() : string{
		return "땅 삭제";
	}
}