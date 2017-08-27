<?php

namespace solo\sololand\command\defaults\land\args;

use pocketmine\command\CommandSender;
use solo\sololand\command\SubCommand;
use solo\sololand\world\World;
use solo\solocore\util\Message;

class LandSellList extends SubCommand{

	public function __construct(){
		parent::__construct("매물", "판매중인 땅의 목록을 확인합니다.", ["매물목록", "매물리스트", "판매목록", "판매리스트"], [
				["페이지"]
		]);
		$this->setPermission("sololand.command.land.selllist");
	}

	public function execute(CommandSender $sender, array $args){
		$world = World::getWorld($sender);
		$information = [];
		$page = 1;
		if(isset($args[0]) && is_numeric($args[0])){
			$page = (int) $args[0];
		}
		foreach($world->getLandProvider()->getLands() as $land){
			if(!$land->isSail()){
				continue;
			}
			$line = "";
			if($land->hasOwner()){
				$line .= "§l§a" . $land->getOwner() . "님§r§a이 §l§a" . $land->getId() . "번§r§a땅을 §l§a" . $land->getPrice() . "원§r§a에 판매중입니다.";
			}else{
				$line .= "§l§a" . $land->getId() . "번§r§a땅이 §l§a" . $land->getId() . "원§r§a에 판매중입니다.";
			}
			if($land->getWelcomeMessage() !== ""){
				$line .= "§r§7 - " . $land->getWelcomeMessage();
			}
			$information[] = $line;
		}
		Message::page($sender, "판매중인 땅 목록", $information, $page);
		return true;
	}
}