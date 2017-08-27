<?php

namespace solo\sololand\event\room;

use pocketmine\Player;
use pocketmine\event\Cancellable;
use solo\sololand\land\Room;

class RoomLeaveEvent extends RoomEvent implements Cancellable{

	public static $handlerList = null;

	protected $player;
	protected $leaveMessage;

	public function __construct(Player $player, Room $room, string $leaveMessage = ""){
		$this->player = $player;
		$this->room = $room;
		$this->leaveMessage = $leaveMessage;
	}
	
	public function getLeaveMessage() : string{
		return $this->leaveMessage;
	}
	
	public function getPlayer(Player $player){
		return $this->player;
	}
	
	public function setLeaveMessage(string $leaveMessage){
		$this->leaveMessage = $leaveMessage;
	}
}
