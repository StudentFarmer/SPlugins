<?php

namespace solo\sololand\command\defaults\land\args;

use pocketmine\command\CommandSender;

use solo\sololand\command\SubCommand;
use solo\sololand\world\World;
use solo\solocore\util\Message;

class LandAccess extends SubCommand{

	public function __construct(){
		parent::__construct("출입허용", "다른 유저의 땅 출입 허용 여부를 설정합니다.", ["출입금지", "출입", "접근", "접근허용", "접근거부"]);
		$this->setPermission("sololand.command.land.access");
	}

	public function execute(CommandSender $sender, array $args){
		$world = World::getWorld($sender);
		$land = $world->getLandProvider()->getLand($sender);
		if($land === null){
			Message::alert($sender, "현재 위치에서 땅을 찾을 수 없습니다.");
			return true;
		}
		if(!$sender->hasPermission("sololand.administrate.land.modify") && !$land->isOwner($sender)){
			Message::alert($sender, "땅 주인이 아니므로 땅 출입허용 여부를 설정할 수 없습니다.");
			return true;
		}
		$land->setAllowAccess(!$land->isAllowAccess());
		Message::normal($sender, $land->isAllowAccess() ? "다른 유저의 출입을 허용하도록 설정하였습니다." : "다른 유저의 출입을 허용하지 않도록 설정하였습니다.");
		return true;
	}
}
