<?php

namespace solo\sololand\command\defaults\land\args;

use pocketmine\command\CommandSender;
use solo\sololand\command\SubCommand;
use solo\sololand\world\World;
use solo\solocore\util\Message;

class LandSetSpawn extends SubCommand{

	public function __construct(){
		parent::__construct("스폰", "땅 이동시 텔레포트될 지점을 설정합니다.");
		$this->setPermission("sololand.command.land.setspawn");
	}

	public function execute(CommandSender $sender, array $args){
		$world = World::getWorld($sender);
		$land = $world->getLandProvider()->getLand($sender);
		if($land === null){
			Message::alert($sender, "현재 위치에서 땅을 찾을 수 없습니다.");
			return true;
		}
		if(!$sender->hasPermission("sololand.administrate.land.modify") && !$land->isOwner($sender)){
			Message::alert($sender, "땅 주인이 아니므로 땅 스폰을 수정할 수 없습니다.");
			return true;
		}
		$land->setSpawnPoint($sender);
		Message::normal($sender, "땅 스폰 위치를 변경하였습니다.");
		return true;
	}
}
