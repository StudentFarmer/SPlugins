<?php

namespace solo\sololand\command\defaults\land\args;

use pocketmine\command\CommandSender;
use solo\sololand\command\SubCommand;
use solo\sololand\world\World;
use solo\sololand\land\Land;
use solo\solocore\util\Message;

class LandCancelSell extends SubCommand{
 
	public function __construct(){
		parent::__construct("판매취소", "판매중인 땅의 판매를 취소합니다.", ["팔기취소"]);
		$this->setPermission("sololand.command.land.cancelsell");
	}

	public function execute(CommandSender $sender, array $args){
		$world = World::getWorld($sender);
		$land = $world->getLandProvider()->getLand($sender);
		if($land === null){
			Message::alert($sender, "현재 위치에서 땅을 찾을 수 없습니다.");
			return true;
		}
		if(!$sender->hasPermission("sololand.administrate.land.modify") && !$land->isOwner($sender)){
			Message::alert($sender, "땅 주인이 아니므로 땅 판매 여부를 수정할 수 없습니다.");
			return true;
		}
		if(!$land->isSail()){
			Message::alert($sender, "이 땅은 현재 판매중이 아닙니다.");
			return true;
		}
		$land->setPrice(-1);
		Message::normal($sender, "땅 판매를 취소하였습니다.");
		return true;
	}
}