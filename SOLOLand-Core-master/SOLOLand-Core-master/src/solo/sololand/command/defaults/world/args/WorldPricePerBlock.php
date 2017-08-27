<?php

namespace solo\sololand\command\defaults\world\args;

use pocketmine\command\CommandSender;
use solo\sololand\command\SubCommand;
use solo\sololand\world\World;

class WorldPricePerBlock extends SubCommand{

	public function __construct(){
		parent::__construct("땅블럭당가격", "땅 생성시 한 블럭당 가격을 설정합니다.", null, [
				["가격"]
		]);
		$this->setPermission("sololand.command.world.priceperblock");
	}

	public function execute(CommandSender $sender, array $args){
		if(!isset($args[0]) || !is_numeric($args[0])){
			return false;
		}
		$price = (float) $args[0];
		$world = World::getWorld($sender);
		$world->getLandProperties()->setPricePerBlock($price);
		Message::normal($sender, $world->getName() . " 월드의 블럭당 가격을 " . $price . "원으로 설정하였습니다.");
		return true;
	}
}