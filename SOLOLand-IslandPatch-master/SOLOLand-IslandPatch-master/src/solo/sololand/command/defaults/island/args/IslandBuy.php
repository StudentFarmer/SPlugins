<?php

namespace solo\sololand\command\defaults\island\args;

use pocketmine\command\CommandSender;
use solo\sololand\command\IslandSubCommand;
use solo\sololand\world\World;
use solo\solocore\util\Message;
use solo\solocore\util\Economy;

class IslandBuy extends IslandSubCommand{
	
	public function __construct(World $world){
		parent::__construct("구매", "섬을 구매합니다.");
		$this->setPermission("sololand.command.island.buy");
		$this->world = $world;
		$this->setInGameOnly(false);
	}
	
	public function execute(CommandSender $sender, array $args){
		$world = $this->world;
		$myMoney = 20000;//Economy::getMoney($sender);
		$price = $world->getLandProperties()->getDefaultPrice();
		
		if(!$sender->hasPermission("sololand.administrate.land.ignoreMaxCount") && $world->getLandProperties()->getMaxCountPerPlayer() <= count($world->getLands(function(Land $land) use ($sender){ return $land->isOwner($sender); }))){
			Message::alert($sender, "해당 월드에서 소유할 수 있는 땅의 최대 갯수를 초과하였습니다. (최대 " . $world->getLandProperties()->getMaxCountPerPlayer() . "개)");
			return true;
		}

		if(!$sender->hasPermission("sololand.administrate.land.ignoreMoney")){
			if($myMoney < $price){
				Message::alert($sender, "섬을 구매할 돈이 부족합니다. 섬 가격 : " . $price);
				return true;
			}
			Economy::reduceMoney($sender, $price);
		}
		$land = $world->createLand();
		$land->setOwner($sender);
		
		$world->getLandManager()->addLand($land);
		Message::normal($sender, "성공적으로 섬을 구매하였습니다. 섬 번호는 " . $land->getId() . "번 입니다.");
		return true;
	}
}