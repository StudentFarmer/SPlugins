<?php

namespace solo\sololand\command\defaults\land\args;

use pocketmine\command\CommandSender;
use solo\sololand\command\SubCommand;
use solo\sololand\world\World;
use solo\sololand\land\Land;
use solo\solocore\util\Message;

class LandShareList extends SubCommand{

	public function __construct(){
		parent::__construct("공유목록", "공유받은 땅의 목록을 확인합니다.", null, [
				["페이지"],
				["유저", "페이지"]
		]);
		$this->setPermission("sololand.command.land.sharelist");
	}

	public function execute(CommandSender $sender, array $args){
		$targetName = $sender->getName();
		$page = 1;
		if(isset($args[0])){
			if(is_numeric($args[0])){
				$page = (int) $args[0];
			}else{
				$targetName = $args[0];
			}
			if(isset($args[1]) && is_numeric($args[1])){
				$page = (int) $args[1];
			}
		}
		$information = [];
		foreach(World::getWorlds() as $world){
			foreach($world->getLandProvider()->getLands(function (Land $land) use ($sender) { return !$land->isOwner($sender) && $land->isMember($sender); } ) as $land){
				$line = "§l§a[" . $world->getName() . " 월드] " . $land->getId() . "번땅 §r§7(" . $land->getWidth() . "x" . $land->getDepth() . ")";
				if($land->getWelcomeMessage() !== ""){
					$line .= " - " . $land->getWelcomeMessage();
				}
				$information[] = $line;
			}
		}
		if(count($information) == 0){
			Message::normal($sender, $targetName . "님은 공유받은 땅이 없습니다.");
		}else{
			Message::page($sender, $targetName . "님의 공유받은 땅 목록", $information, $page);
		}
		return true;
	}
}