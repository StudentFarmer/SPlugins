<?php

namespace solo\sololand\event\land;

use pocketmine\Player;
use pocketmine\event\Cancellable;
use solo\sololand\land\Land;

class LandLeaveEvent extends LandEvent implements Cancellable{

	public static $handlerList = null;

	protected $player;
	protected $leaveMessage;

	public function __construct(Player $player, Land $land, string $leaveMessage){
		$this->player = $player;
		$this->land = $land;
		$this->leaveMessage = $leaveMessage;
	}

	public function getLeaveMessage() : string{
		return $this->leaveMessage;
	}

	public function getPlayer() : Player{
		return $this->player;
	}

	public function setLeaveMessage(string $leaveMessage){
		$this->leaveMessage = $leaveMessage;
	}
}
