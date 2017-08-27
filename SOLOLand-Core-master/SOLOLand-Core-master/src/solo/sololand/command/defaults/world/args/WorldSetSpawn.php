<?php

namespace solo\sololand\command\defaults\world\args;

use pocketmine\command\CommandSender;
use solo\sololand\command\SubCommand;
use solo\sololand\world\World;
use solo\solocore\util\Message;

class WorldSetSpawn extends SubCommand{

	public function __construct(){
		parent::__construct("스폰", "월드의 스폰을 설정합니다.");
		$this->setPermission("sololand.command.world.setspawn");
	}

	public function execute(CommandSender $sender, array $args){
		$world = World::getWorld($sender);
		$world->getLevel()->setSpawnLocation($sender);
		Message::normal($sender, $world->getName() . " 월드의 스폰위치를 변경하였습니다.");
		return true;
	}
}