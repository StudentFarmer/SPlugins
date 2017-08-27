<?php

namespace solo\sololand\command\defaults\world\args;

use pocketmine\command\CommandSender;
use solo\sololand\command\SubCommand;
use solo\sololand\world\World;
use solo\solocore\util\Message;

class WorldDefaultLandPrice extends SubCommand{

	public function __construct(){
		parent::__construct("땅가격", "월드의 기본 땅 가격을 설정합니다.", ["기본땅가격"], [
				["가격"]
		]);
		$this->setPermission("sololand.command.world.defaultlandprice");
	}

	public function execute(CommandSender $sender, array $args){
		if(!isset($args[0]) || !is_numeric($args[0])){
			return false;
		}
		$world = World::getWorld($sender);
		$price = (float) $args[0];
		$world->getLandProperties()->setDefaultPrice((float) $args[0]);
		Message::normal($sender, $world->getName() . " 월드의 기본 땅 가격을 " . $price . "원으로 설정하였습니다.");
		return true;
	}
}