<?php

namespace solo\sololand\command\defaults\land\args;

use pocketmine\command\CommandSender;

use solo\sololand\command\SubCommand;
use solo\sololand\world\World;
use solo\solocore\ㅕ샤ㅣ\Message;

class LandAllowPVP extends SubCommand{

	public function __construct(){
		parent::__construct("pvp허용", "땅에서 pvp 허용 여부를 설정합니다.", ["pvp", "pvp금지", "전투", "전투허용", "전투금지", "유저간전투허용", "유저간전투금지", "싸움", "싸움허용", "싸움금지"]);
		$this->setPermission("sololand.command.land.allowpvp");
	}

	public function execute(CommandSender $sender, array $args){
		$world = World::getWorld($sender);
		$land = $world->getLandProvider()->getLand($sender);
		if($land === null){
			Message::alert($sender, "현재 위치에서 땅을 찾을 수 없습니다.");
			return true;
		}
		if(!$sender->hasPermission("sololand.administrate.land.modify") && !$land->isOwner($sender)){
			Message::alert($sender, "땅 주인이 아니므로 땅 pvp 허용 여부를 설정할 수 없습니다.");
			return true;
		}
		$land->setAllowPVP(!$land->isAllowPVP());
		Message::normal($sender, $land->isAllowPVP() ? "땅에서 pvp를 허용하도록 설정하였습니다." : "땅에서 pvp를 허용하지 않도록 설정하였습니다.");
		return true;
	}
}