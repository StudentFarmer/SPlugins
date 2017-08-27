<?php

namespace solo\sololand\command\defaults\world\args;

use pocketmine\Server;
use pocketmine\command\CommandSender;
use solo\sololand\command\SubCommand;
use solo\solocore\util\Message;

class WorldLoad extends SubCommand{

	public function __construct(){
		parent::__construct("로드", "월드를 로드합니다.", ["불러오기"], [
				["월드 이름"]
		]);
		$this->setInGameOnly(false);
		$this->setPermission("sololand.command.world.load");
	}

	public function execute(CommandSender $sender, array $args){
		if(!isset($args[0])){
			return false;
		}
		$levelName = implode($args);
		if(Server::getInstance()->loadLevel($levelName)){
			Message::normal($sender, $levelName . " 월드를 성공적으로 로드하였습니다.");
		}else{
			Message::alert($sender, $levelName . " 월드를 로드하지 못했습니다. 월드 이름을 다시 한번 확인해주세요.");
		}
		return true;
	}
}