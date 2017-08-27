<?php

namespace solo\sololand\command\defaults\land\args;

use pocketmine\Server;
use pocketmine\command\CommandSender;
use solo\sololand\command\SubCommand;
use solo\sololand\world\World;
use solo\sololand\land\Land;
use solo\solocore\util\Message;
use solo\solonotification\Notification;

class LandCancelShare extends SubCommand{

	public function __construct(){
		parent::__construct("공유취소", "공유중이던 유저의 공유를 취소합니다.", null, [
				["유저"],
				["유저", "유저", "유저..."]
		]);
		$this->setPermission("sololand.command.land.cancelshare");
	}

	public function execute(CommandSender $sender, array $args){
		$world = World::getWorld($sender);
		$land = $world->getLandProvider()->getLand($sender);
		if($land === null){
			Message::alert($sender, "현재 위치에서 땅을 찾을 수 없습니다.");
			return true;
		}
		if(!$sender->hasPermission("sololand.administrate.land.modify") && !$land->isOwner($sender)){
			Message::alert($sender, "땅 주인이 아니므로 땅 공유목록을 수정할 수 없습니다.");
			return true;
		}
		if(!isset($args[0])){
			return false;
		}
		foreach($args as $arg){
			$target = Server::getInstance()->getPlayer($arg);
			$targetName;
			if($target === null){
				$targetName = $arg;
			}else{
				$targetName = $target->getName();
			}
			if (!$land->isMember($targetName)){
				Message::alert($sender, $targetName . "님은 공유 목록에 없습니다.");
				continue;
			}
			$land->removeMember($targetName);
			Message::normal($sender, $targetName . "님을 공유 취소 하였습니다.");
			
			@Notification::addNotification($targetName, $world->getName() . " 월드의 " . $land->getId() . "번 땅 공유가 취소되었습니다.");
		}
		return true;
	}
}