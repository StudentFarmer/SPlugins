<?php

namespace solo\sololand\command\defaults\island\args;

use pocketmine\command\CommandSender;
use pocketmine\level\Position;
use solo\sololand\command\IslandSubCommand;
use solo\sololand\world\World;
use solo\solocore\util\Message;

class IslandMove extends IslandSubCommand{
	
	public function __construct(World $world){
		parent::__construct("이동", "해당 번호의 섬으로 이동합니다.", [
				["섬 번호"]
		]);
		$this->setPermission("sololand.command.island.move");
		$this->world = $world;
	}
	
	public function execute(CommandSender $sender, array $args){
		if(!isset($args[0]) || !is_numeric($args[0])){
			return false;
		}
		$id = (int) $args[0];
		$world = $this->getWorld();
		$land = $world->getLandManager()->getLandById($id);
		if($land === null){
			Message::alert($sender, $id . "번 섬은 존재하지 않습니다.");
			return true;
		}
		if(
				!$land->isAllowAccess() &&
				!$sender->hasPermission("sololand.administrate.land.access") &&
				!$land->isOwner($sender) &&
				!$land->isMember($sender)
				){
					Message::alert($sender, $id . "번 섬은 현재 출입이 제한되어 있습니다.");
					return true;
		}
		$spawnVector = $land->getSpawnPoint();
		$sender->teleport(new Position($spawnVector->x, $spawnVector->y, $spawnVector->z, $world->getLevel()));
		Message::normal($sender, $id . "번 섬으로 이동하였습니다.");
		return true;
	}
}