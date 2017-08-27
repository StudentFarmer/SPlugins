<?php

namespace solo\sololand\command\defaults\land\args;

use pocketmine\command\CommandSender;
use solo\sololand\command\SubCommand;
use solo\sololand\world\World;
use solo\solocore\util\Message;

class LandWelcomeMessage extends SubCommand{

	public function __construct(){
		parent::__construct("환영말", "다른 유저가 땅 방문시 보낼 메세지를 설정합니다.", ["환영메세지", "입장말", "입장메세지"], [
				["환영말..."]
		]);
		$this->setPermission("sololand.command.land.welcomemessage");
	}

	public function execute(CommandSender $sender, array $args){
		$world = World::getWorld($sender);
		$land = $world->getLandProvider()->getLand($sender);
		if($land === null){
			Message::alert($sender, "현재 위치에서 땅을 찾을 수 없습니다.");
			return true;
		}
		if(!$sender->hasPermission("sololand.administrate.land.modify") && !$land->isOwner($sender)){
			Message::alert($sender, "땅 주인이 아니므로 땅 환영말을 수정할 수 없습니다.");
			return true;
		}
		if(!isset($args[0])){
			return false;
		}
		$land->setWelcomeMessage(implode(" ", $args));
		Message::normal($sender, "성공적으로 환영말을 설정하였습니다 : " . $land->getWelcomeMessage());
		return true;
	}
}