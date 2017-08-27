<?php

namespace solo\sololand\command\defaults\land\args;

use pocketmine\command\CommandSender;
use solo\sololand\command\SubCommand;
use solo\sololand\world\World;
use solo\solocore\util\Message;
use solo\solocore\util\ParticleUtil;

class LandWelcomeParticle extends SubCommand{

	public function __construct(){
		parent::__construct("환영효과", "다른 유저가 땅 방문시 나타낼 효과를 설정합니다.", ["환영파티클", "입장파티클", "입장효과"], [
				[implode("/", ParticleUtil::getAvailable()) . " 또는 제거"]
		]);
		$this->setPermission("sololand.command.land.welcomeparticle");
	}

	public function execute(CommandSender $sender, array $args){
		$world = World::getWorld($sender);
		$land = $world->getLandProvider()->getLand($sender);
		if($land === null){
			Message::alert($sender, "현재 위치에서 땅을 찾을 수 없습니다.");
			return true;
		}
		if(!$sender->hasPermission("sololand.administrate.land.modify") && !$land->isOwner($sender)){
			Message::alert($sender, "땅 주인이 아니므로 땅 환영 효과를 수정할 수 없습니다.");
			return true;
		}
		if(!isset($args[0])){
			return false;
		}
		$particleId = ParticleUtil::fromString($args[0]);
		if($particleId == 0 && $args[0] !== "제거"){
			return false;
		}
		$land->setWelcomeParticle($particleId);
		Message::normal($sender, $particleId == 0 ? "성공적으로 환영 효과를 제거하였습니다" : "성공적으로 환영 효과를 설정하였습니다 : " . $args[0]);
		return true;
	}
}