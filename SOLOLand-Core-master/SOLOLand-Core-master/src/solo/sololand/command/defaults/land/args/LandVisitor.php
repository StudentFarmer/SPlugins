<?php

namespace solo\sololand\command\defaults\land\args;

use pocketmine\command\CommandSender;
use solo\sololand\command\SubCommand;
use solo\sololand\world\World;
use solo\sololand\land\Land;
use solo\solocore\util\Message;

class LandVisitor extends SubCommand{

	public function __construct(){
		parent::__construct("방문자", "현재 땅을 방문중인 유저 목록을 확인합니다.", ["방문목록"]);
		$this->setPermission("sololand.command.land.visitor");
	}

	public function execute(CommandSender $sender, array $args){
		$world = World::getWorld($sender);
		$land = $world->getLandProvider()->getLand($sender);

		if($land === null){
			Message::alert($sender, "현재 위치에서 땅을 찾을 수 없습니다.");
			return true;
		}

		$players = [];
		foreach($world->getLevel()->getPlayers() as $player){
			if($land->isInside($player)){
				$players[] = $player->getName();
			}
		}

		Message::normal($sender, $land->getId() . "번 땅 방문자 : " . implode(", ", $players) . " ( " . count($players) .  "명 )");
		return true;
	}
}