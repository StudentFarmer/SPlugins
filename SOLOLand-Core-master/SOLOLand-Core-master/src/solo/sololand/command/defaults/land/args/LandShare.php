<?php

namespace solo\sololand\command\defaults\land\args;

use pocketmine\Server;
use pocketmine\command\CommandSender;
use solo\sololand\command\SubCommand;
use solo\sololand\world\World;
use solo\solocore\util\Message;
use solo\solonotification\Notification;

class LandShare extends SubCommand{

	public function __construct(){
		parent::__construct("공유", "다른 유저와 땅을 공유합니다.", ["같이쓰기"], [
				["유저"],
				["유저", "유저", "유저..."]
		]);
		$this->setPermission("sololand.command.land.share");
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
			if($target === null){
				Message::alert($sender, $arg . "님은 현재 온라인이 아닙니다.");
				continue;
			}
			if($land->isOwner($target)){
				Message::alert($sender, "땅을 주인에게 공유할 수 없습니다.");
				continue;
			}
			if ($land->isMember($target)){
				Message::alert($sender, $target->getName() . "님은 이미 공유 되어있습니다.");
				continue;
			}
			$land->addMember($target);
			Message::normal($sender, $target->getName() . "님에게 땅을 공유하였습니다.");
			
			@Notification::addNotification($target, $sender->getName() . "님이 " . $world->getName() . " 월드의 " . $land->getId() . "번 땅을 공유하셨습니다.");
		}
		return true;
	}
}