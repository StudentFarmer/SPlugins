<?php

namespace solo\sololand\command\defaults\world\args;

use pocketmine\command\CommandSender;
use solo\sololand\command\SubCommand;
use solo\sololand\world\World;
use solo\solocore\util\Message;

class WorldAllowPVP extends SubCommand{

	public function __construct(){
		parent::__construct("pvp허용", "유저간 pvp 허용 여부를 설정합니다.", ["pvp", "전투", "pvp금지", "전투허용", "전투금지", "유저간전투허용", "유저간전투금지", "싸움", "싸움허용", "싸움금지"]);
		$this->setPermission("sololand.command.world.allowpvp");
	}

	public function execute(CommandSender $sender, array $args){
		$world = World::getWorld($sender);
		$world->getWorldProperties()->setAllowPVP(!$world->getWorldProperties()->isAllowPVP());
		Message::normal($sender, $world->getName() . " 월드의 PVP를 " . ($world->getWorldProperties()->isAllowPVP() ? "허용하였습니다." : "금지하였습니다."));
		return true;
	}
}