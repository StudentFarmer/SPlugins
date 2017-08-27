<?php

namespace solo\sololand\command\defaults\world\args;

use pocketmine\command\CommandSender;
use solo\sololand\command\SubCommand;
use solo\sololand\world\World;

class WorldMaxLandCount extends SubCommand{

	public function __construct(){
		parent::__construct("땅최대갯수", "1인당 최대 소지 가능한 땅 갯수를 설정합니다.", null, [
				["갯수"]
		]);
		$this->setPermission("sololand.command.world.maxlandcount");
	}

	public function execute(CommandSender $sender, array $args){
		if(!isset($args[0]) || !is_numeric($args[0])){
			return false;
		}
		$count = (int) $args[0];
		$world = World::getWorld($sender);
		$world->getLandProperties()->setMaxCountPerPlayer($count);
		Message::normal($sender, $world->getName() . " 월드의 1인당 땅 최대 소지 가능 갯수를 " . $count . "개로 설정하였습니다.");
		return true;
	}
}