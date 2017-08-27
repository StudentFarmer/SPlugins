<?php

namespace solo\sololand\command\defaults\world\args;

use pocketmine\command\CommandSender;
use solo\sololand\command\SubCommand;
use solo\sololand\world\World;

class WorldAllowExplosion extends SubCommand{

	public function __construct(){
		parent::__construct("폭발허용", "TNT 폭파시 블럭 파괴 허용 여부를 설정합니다.", ["tnt", "폭발"]);
		$this->setPermission("sololand.command.world.allowexplosion");
	}

	public function execute(CommandSender $sender, array $args){
		$world = World::getWorld($sender);
		$world->getWorldProperties()->setAllowExplosion(!$world->getWorldProperties()->isAllowExplosion());
		Message::normal($sender, $world->getName() . " 월드의 TNT 블럭 파괴를 " . ($world->getWorldProperties()->isAllowExplosion() ? "허용하였습니다." : "금지하였습니다."));
		return true;
	}
}