<?php

namespace solo\sololand\command\defaults\world\args;

use pocketmine\command\CommandSender;
use solo\sololand\command\SubCommand;
use solo\sololand\world\World;
use solo\solocore\util\Message;

class WorldProtect extends SubCommand{

	public function __construct(){
		parent::__construct("보호", "월드의 보호 여부를 설정합니다.");
		$this->setPermission("sololand.command.world.protect");
	}

	public function execute(CommandSender $sender, array $args){
		$world = World::getWorld($sender);
		$world->getWorldProperties()->setProtected(!$world->getWorldProperties()->isProtected());
		Message::normal($sender, $world->getName() . " 월드의 보호를 " . ($world->getWorldProperties()->isProtected() ? $world->getName() . "켰습니다." : "해제하였습니다."));
		return true;
	}
}