<?php

namespace solo\sololand\command\defaults\island\args;

use pocketmine\command\CommandSender;
use solo\sololand\command\IslandSubCommand;
use solo\sololand\world\World;
use solo\solocore\util\Message;

class IslandList extends IslandSubCommand{
	
	public function __construct(World $world){
		parent::__construct("목록", "자신이 소유한 섬 목록을 봅니다.", [
				["페이지"],
				["유저", "페이지"]
		]);
		$this->setPermission("sololand.command.island.list");
		$this->world = $world;
	}
	
	public function execute(CommandSender $sender, array $args){
		$world = $this->getWorld();
		
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
				$targetName = $args[0];
			}
		}
		$information = [];
		foreach($world->getLandManager()->getLands(function (Land $land) use ($sender) { return $land->isOwner($sender); } ) as $land){
			$line = "§l§a[" . $world->getName() . " 월드] " . $land->getId() . "번땅 §r§7(" . $land->getWidth() . "x" . $land->getDepth() . ")";
			if($land->getWelcomeMessage() !== ""){
				$line .= " - " . $land->getWelcomeMessage();
			}
			$information[] = $line;
		}
		if(count($information) == 0){
			Message::normal($sender, $targetName . "님은 소유중인 섬이 없습니다.");
		}else{
			Message::page($sender, $targetName . "님의 섬 목록", $information, $page);
		}
		return true;
	}
}