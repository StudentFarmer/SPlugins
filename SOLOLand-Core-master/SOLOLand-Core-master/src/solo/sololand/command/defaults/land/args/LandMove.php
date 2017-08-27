<?php

namespace solo\sololand\command\defaults\land\args;

use pocketmine\command\CommandSender;
use solo\sololand\command\SubCommand;
use solo\sololand\world\World;
use solo\solocore\util\Message;

class LandMove extends SubCommand{

	public function __construct(){
		parent::__construct("이동", "해당 번호의 땅으로 이동합니다.", ["워프"], [
				["땅 번호"]
		]);
		$this->setPermission("sololand.command.land.move");
	}

	public function execute(CommandSender $sender, array $args){
		if(!isset($args[0]) || !is_numeric($args[0])){
			return false;
		}
		$id = (int) $args[0];
		$world = World::getWorld($sender);
		$land = $world->getLandProvider()->getLandById($id);
		if($land === null){
			Message::alert($sender, $id . "번 땅은 존재하지 않습니다.");
			return true;
		}
		if(
			!$land->isAllowAccess() &&
			!$sender->hasPermission("sololand.administrate.land.access") &&
			!$land->isOwner($sender) &&
			!$land->isMember($sender)
		){
			Message::alert($sender, $id . "번 땅은 현재 출입이 제한되어 있습니다.");
			return true;
		}
		$sender->teleport($land->getSpawnPoint());
		Message::normal($sender, $id . "번 땅으로 이동하였습니다.");
		return true;
	}
}