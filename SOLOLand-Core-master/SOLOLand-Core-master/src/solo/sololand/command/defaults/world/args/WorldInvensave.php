<?php

namespace solo\sololand\command\defaults\world\args;

use pocketmine\command\CommandSender;
use solo\sololand\command\SubCommand;
use solo\sololand\world\World;
use solo\solocore\util\Message;

class WorldInvenSave extends SubCommand{

	public function __construct(){
		parent::__construct("인벤세이브", "월드의 인벤세이브 여부를 설정합니다.");
		$this->setPermission("sololand.command.world.invensave");
	}

	public function execute(CommandSender $sender, array $args){
		$world = World::getWorld($sender);
		$world->getWorldProperties()->setInvensave(!$world->getWorldProperties()->isInvensave());
		Message::normal($sender, $world->getName() . " 월드의 인벤세이브를 " . ($world->getWorldProperties()->isInvensave() ? "켰습니다." : "해제하였습니다."));
		return true;
	}
}
