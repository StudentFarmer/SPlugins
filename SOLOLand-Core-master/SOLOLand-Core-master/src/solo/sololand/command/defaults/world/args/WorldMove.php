<?php

namespace solo\sololand\command\defaults\world\args;

use pocketmine\Server;
use pocketmine\command\CommandSender;
use solo\sololand\command\SubCommand;
use solo\solocore\util\Message;

class WorldMove extends SubCommand{

	public function __construct(){
		parent::__construct("이동", "해당 월드로 이동합니다.", null, [
				["월드 이름"]
		]);
		$this->setPermission("sololand.command.world.move");
	}

	public function execute(CommandSender $sender, array $args){
		if(!isset($args[0])){
			return false;
		}
		$level = Server::getInstance()->getLevelByName($args[0]);
		if($level === null){
			Message::alert($sender, $args[0] . " 은(는) 존재하지 않는 월드입니다.");
			return true;
		}
		$sender->teleport($level->getSpawnLocation());
		Message::normal($sender, $level->getFolderName() . " 월드로 이동하였습니다.");
		return true;
	}
}