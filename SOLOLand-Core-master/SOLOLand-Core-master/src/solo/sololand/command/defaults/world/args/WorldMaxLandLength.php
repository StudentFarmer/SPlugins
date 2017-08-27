<?php

namespace solo\sololand\command\defaults\world\args;

use pocketmine\command\CommandSender;
use solo\sololand\command\SubCommand;
use solo\sololand\world\World;

class WorldMaxLandLength extends SubCommand{

	public function __construct(){
		parent::__construct("땅최대길이", "땅의 한변 최대 길이를 설정합니다.", null, [
				["길이(단위:블럭)"]
		]);
		$this->setPermission("sololand.command.world.maxlandlength");
	}

	public function execute(CommandSender $sender, array $args){
		if(!isset($args[0]) || !is_numeric($args[0])){
			return false;
		}
		$length = (int) $args[0];
		$world = World::getWorld($sender);
		$world->getLandProperties()->setMaxLength($length);
		Message::normal($sender, $world->getName() . " 월드의 땅 한변 최대 길이를 " . $length . "로 설정하였습니다.");
		return true;
	}
}