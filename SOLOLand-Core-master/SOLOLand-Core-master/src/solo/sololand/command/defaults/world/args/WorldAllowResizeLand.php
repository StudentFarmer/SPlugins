<?php

namespace solo\sololand\command\defaults\world\args;

use pocketmine\command\CommandSender;
use solo\sololand\command\SubCommand;
use solo\sololand\world\World;

class WorldAllowResizeLand extends SubCommand{

	public function __construct(){
		parent::__construct("땅크기변경허용", "땅을 축소 또는 확장할 수 있는지 여부를 설정합니다.");
		$this->setPermission("sololand.command.world.allowresizeland");
	}

	public function execute(CommandSender $sender, array $args){
		$world = World::getWorld($sender);
		$world->getLandProperties()->setAllowResize(!$world->getLandProperties()->isAllowResize());
		Message::normal($sender, $world->getName() . " 월드에서 땅의 크기를 변경할 수 " . ($world->getLandProperties()->isAllowResize() ? "없도록 설정하였습니다." : "있도록 설정하였습니다."));
		return true;
	}
}