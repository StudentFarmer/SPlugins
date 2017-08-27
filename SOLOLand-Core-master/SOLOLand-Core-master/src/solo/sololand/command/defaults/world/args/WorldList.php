<?php

namespace solo\sololand\command\defaults\world\args;

use pocketmine\command\CommandSender;
use solo\sololand\command\SubCommand;
use solo\sololand\world\World;
use solo\solocore\util\Message;

class WorldList extends SubCommand{

	public function __construct(){
		parent::__construct("목록", "월드 목록을 확인합니다.", ["리스트"], [
				["페이지"]
		]);
		$this->setInGameOnly(false);
		$this->setPermission("sololand.command.world.list");
	}

	public function execute(CommandSender $sender, array $args){
		$lines = [];
		$page = 1;
		if(isset($args[0]) && is_numeric($args[0])){
			$page = (int) $args[0];
		}
		foreach(World::getWorlds() as $world){
			$lines[] = $world->getName() . " (Level 이름 : " . $world->getLevel()->getName() . ", 제너레이터 : " . $world->getLevel()->getProvider()->getGenerator() . ")";
		}
		Message::page($sender, "월드 목록", $lines, $page);
		return true;
	}
}