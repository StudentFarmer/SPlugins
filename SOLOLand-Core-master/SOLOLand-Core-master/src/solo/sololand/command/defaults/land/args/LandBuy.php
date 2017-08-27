<?php

namespace solo\sololand\command\defaults\land\args;

use pocketmine\command\CommandSender;
use solo\sololand\command\SubCommand;
use solo\sololand\world\World;
use solo\sololand\land\Land;
use solo\sololand\util\Queue;
use solo\solocore\util\Message;
use solo\solocore\util\Economy;
use solo\solonotification\Notification;

class LandBuy extends SubCommand{

	public function __construct(){
		parent::__construct("구매", "판매중인 땅을 구매합니다.", ["구입"]);
		$this->setPermission("sololand.command.land.buy");
	}

	public function execute(CommandSender $sender, array $args){
		$world = World::getWorld($sender);
		$land = $world->getLandProvider()->getLand($sender);
		if($land === null){
			Message::alert($sender, "현재 위치에서 땅을 찾을 수 없습니다.");
			return true;
		}
		if(!$land->isSail()){
			Message::alert($sender, "이 땅은 현재 판매중이 아닙니다.");
			return true;
		}
		if($land->isOwner($sender)){
			Message::alert($sender, "땅 주인이 땅을 구매할 수 없습니다. 판매를 취소하려면 /땅 판매취소 명령어를 입력해주세요.");
			return true;
		}
		if(
			!$sender->hasPermission("sololand.administrate.land.ignoreMaxCount")
			&& $world->getLandProperties()->getMaxCountPerPlayer() <= count($world->getLandProvider()->getLands(function(Land $land) use($sender) { return $land->isOwner($sender); }))
		){
			Message::alert($sender, "해당 월드에서 소유할 수 있는 땅의 최대 갯수를 초과하였습니다. (최대 " . $world->getLandProperties()->getMaxCountPerPlayer() . "개)");
			return true;
		}
		
		$queue = Queue::getQueue($sender);
		if($queue === null){
			Queue::setQueue($sender, new LandBuyQueue());
			Message::normal($sender, "땅을 정말로 " . $land->getPrice() . " 구매하시겠습니까? 구매하시려면 /땅 구매 명령어를 한번 더 입력해주세요.");
			
		}else if($queue instanceof LandBuyQueue){
			$myMoney = Economy::getMoney($sender);
			if(! $sender->hasPermission("sololand.administrate.land.ignoreMoney")){
				if($myMoney < $land->getPrice()){ 
					Message::alert($sender, "돈이 부족합니다. 내 돈 : " . $myMoney . "원");
					return true;
				}
				Economy::reduceMoney($sender, $land->getPrice());
			}
			
			if($land->hasOwner()){
				Economy::addMoney($land->getOwner(), $land->getPrice());
				@Notification::addNotification($land->getOwner(), $sender->getName() . "님이 " . $world->getName() . " 월드의 " . $land->getId() . "번 땅을 구매하셨습니다.");
			}
			$land->clear(true);
			$land->setOwner($sender);
			Queue::removeQueue($sender);
			Message::normal($sender, $land->getId() . "번 땅을 구매하였습니다.");
			
		}else{
			Message::alert($sender, $queue->getName() . " 작업이 진행중입니다. 해당 작업을 취소한 후 다시 시도해주세요.");
		}
		return true;
	}
}

class LandBuyQueue extends Queue{
	public function getName() : string{
		return "땅 구매";
	}
}