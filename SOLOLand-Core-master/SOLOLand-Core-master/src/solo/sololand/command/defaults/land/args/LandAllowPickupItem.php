<?php

namespace solo\sololand\command\defaults\land\args;

use pocketmine\command\CommandSender;

use solo\sololand\command\SubCommand;
use solo\sololand\world\World;
use solo\solocore\ㅕ샤ㅣ\Message;

class LandAllowPickupItem extends SubCommand{

	public function __construct(){
		parent::__construct("아이템줍기허용", "다른 유저가 아이템을 주울 수 있는지 여부를 설정합니다.", ["아이템줍기", "드랍", "줍기", "줍기허용", "아이템드랍", "드랍허용", "드랍금지", "아이템줍기금지"]);
		$this->setPermission("sololand.command.land.allowpickupitem");
	}

	public function execute(CommandSender $sender, array $args){
		$world = World::getWorld($sender);
		$land = $world->getLandProvider()->getLand($sender);
		if($land === null){
			Message::alert($sender, "현재 위치에서 땅을 찾을 수 없습니다.");
			return true;
		}
		if(!$sender->hasPermission("sololand.administrate.land.modify") && !$land->isOwner($sender)){
			Message::alert($sender, "땅 주인이 아니므로 땅 아이템 줍기 허용 여부를 설정할 수 없습니다.");
			return true;
		}
		$land->setAllowPickupItem(!$land->isAllowPickupItem());
		Message::normal($sender, $land->isAllowPickupItem() ? "다른 유저가 땅에서 아이템을 주울 수 있도록 설정하였습니다." : "다른 유저가 땅에서 아이템을 주울 수 없도록 설정하였습니다.");
		return true;
	}
}