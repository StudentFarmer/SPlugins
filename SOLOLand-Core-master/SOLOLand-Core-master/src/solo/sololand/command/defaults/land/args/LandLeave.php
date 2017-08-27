<?php

namespace solo\sololand\command\defaults\land\args;

use pocketmine\command\CommandSender;
use solo\sololand\command\SubCommand;
use solo\sololand\world\World;
use solo\sololand\land\Land;
use solo\sololand\util\Queue;
use solo\solocore\util\Message;
use solo\solonotification\Notification;

class LandLeave extends SubCommand{

	public function __construct(){
		parent::__construct("나가기", "공유받던 땅에서 나갑니다.", ["떠나기", "소유포기", "소유권포기"]);
		$this->setPermission("sololand.command.land.leave");
	}

	public function execute(CommandSender $sender, array $args){
		$world = World::getWorld($sender);
		$land = $world->getLandProvider()->getLand($sender);
		if($land === null){
			Message::alert($sender, "현재 위치에서 땅을 찾을 수 없습니다.");
			return true;
		}
		if(!$land->isOwner($sender) || !$land->isMember($sender)){
			Message::alert($sender, "이 땅을 공유받고 있지 않습니다.");
			return true;
		}
		
		$queue = Queue::getQueue($sender);
		if($queue === null){
			Queue::setQueue($sender, new LandLeaveQueue());
			$message = ($land->isOwner($sender)) ? "정말로 땅 소유를 포기하시겠습니까? 포기하려면 /땅 나가기 명령어를 한번 더 입력해주세요." : "공유받던 땅에서 나가시겠습니까? 나가시려면 /땅 나가기 명령어를 한번 더 입력해주세요.";
			Message::normal($sender, $message);
				
		}else if($queue instanceof LandLeaveQueue){
			if($land->isOwner($sender)){
				$land->setOwner("");
				$land->setPrice(0);
				Message::normal($sender, $land->getid() . "번 땅의 소유를 포기하였습니다.");
				
			}else if($land->isMember($sender)){
				$land->removeMember($sender);
				@Notification::addNotification($land->getOwner(), $sender->getName() . "님이 " . $land->getId() . "번 땅의 공유를 포기하였습니다.");
				Message::normal($sender, "공유받던 땅에서 나갔습니다.");
			}
			Queue::removeQueue($sender);
			
		}else{
			Message::alert($sender, $queue->getName() . " 작업이 진행중입니다. 해당 작업을 취소한 후 다시 시도해주세요.");
		}
		return true;
	}
}

class LandLeaveQueue extends Queue{
	public function getName() : string{
		return "땅 나가기";
	}
}