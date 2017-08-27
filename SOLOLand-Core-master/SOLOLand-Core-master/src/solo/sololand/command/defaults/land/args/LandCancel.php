<?php

namespace solo\sololand\command\defaults\land\args;

use pocketmine\command\CommandSender;
use solo\sololand\command\SubCommand;
use solo\sololand\util\Queue;
use solo\solocore\util\Message;

class LandCancel extends SubCommand{

	public function __construct(){
		parent::__construct("취소", "진행중인 땅 작업을 취소합니다.", ["작업취소"]);
		$this->setPermission("sololand.command.land.cancel");
	}

	public function execute(CommandSender $sender, array $args){
		$queue = Queue::getQueue($sender);
		if($queue === null){
			Message::alert($sender, "진행중인 작업이 없습니다.");
			return true;
		}
		Queue::removeQueue($sender);
		Message::normal($sender, "진행중이던 작업을 취소하였습니다.");
		return true;
	}
}