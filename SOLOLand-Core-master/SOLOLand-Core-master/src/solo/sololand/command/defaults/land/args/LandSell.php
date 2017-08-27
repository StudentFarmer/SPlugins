<?php

namespace solo\sololand\command\defaults\land\args;

use pocketmine\command\CommandSender;
use solo\sololand\command\SubCommand;
use solo\sololand\world\World;
use solo\sololand\util\Queue;
use solo\solocore\util\Message;

class LandSell extends SubCommand{

	public function __construct(){
		parent::__construct("판매", "땅을 매물에 등록합니다.", ["팔기", "매물등록"], [
				["가격"]
		]);
		$this->setPermission("sololand.command.land.sell");
	}
	
	public function execute(CommandSender $sender, array $args){
		$world = World::getWorld($sender);
		$land = $world->getLandProvider()->getLand($sender);
		
		if($land === null){
			Message::alert($sender, "현재 위치에서 땅을 찾을 수 없습니다.");
			return true;
		}
		if(!$sender->hasPermission("sololand.administrate.land.modify") && !$land->isOwner($sender)){
			Message::alert($sender, "땅 주인이 아니므로 땅을 판매할 수 없습니다.");
			return true;
		}
		if($land->isSail()){
			Message::alert($sender, "땅이 이미 판매중입니다.");
			return true;
		}

		$queue = Queue::getQueue($sender);
		if($queue === null){
			if(!isset($args[0]) || !is_numeric($args[0]) || $args[0] < 0){
				return false;
			}
			$price = (int) $args[0];
			$queue = new LandSellQueue();
			$queue->set("id", $land->getId());
			$queue->set("price", $price);
			Queue::setQueue($sender, $queue);
			Message::normal($sender, "정말로 땅을 " . $price . "원으로 매물목록에 등록하시겠습니까? 등록하려면 /땅 판매 명령어를 한번 더 입력해주세요.");
			
		}else if($queue instanceof LandSellQueue){
			if($land->getId() === $queue->get("id")){
				$price = $queue->get("price");
				$land->setPrice($price);
				Message::normal($sender, "땅을 " . $price . "원으로 매물목록에 등록하였습니다.");
			}else{
				Message::alert($sender, "땅 판매를 진행하던 중 오류가 발생하였습니다. 잠시후 다시 시도해주세요.");
			}
			Queue::removeQueue($sender);
			
		}else{
			Message::alert($sender, $queue->getName() . " 작업이 진행중입니다. 해당 작업을 취소한 후 다시 시도해주세요.");
		}
		return true;
	}
}

class LandSellQueue extends Queue{
	public function getName() : string{
		return "땅 판매";
	}
}