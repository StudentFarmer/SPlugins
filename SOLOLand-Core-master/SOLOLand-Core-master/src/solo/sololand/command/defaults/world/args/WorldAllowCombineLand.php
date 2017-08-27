<?php

namespace solo\sololand\command\defaults\world\args;

use pocketmine\command\CommandSender;
use solo\sololand\command\SubCommand;
use solo\sololand\world\World;

class WorldAllowCombineLand extends SubCommand{

	public function __construct(){
		parent::__construct("땅합치기허용", "땅을 합칠 수 있는지 여부를 설정합니다.");
		$this->setPermission("sololand.command.world.allowcombineland");
	}

	public function execute(CommandSender $sender, array $args){
		$world = World::getWorld($sender);
		$world->getLandProperties()->setAllowCombine(!$world->getLandProperties()->isAllowCombine());
		Message::normal($sender, $world->getName() . " 월드에서 땅을 " . ($world->getLandProperties()->isAllowCombine() ? "합칠 수 있도록 설정하였습니다." : "합칠 수 없도록 설정하였습니다."));
		return true;
	}
}