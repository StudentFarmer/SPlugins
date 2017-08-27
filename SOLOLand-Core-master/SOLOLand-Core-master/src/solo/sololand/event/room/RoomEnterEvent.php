<?php

namespace solo\sololand\event\room;

use pocketmine\Player;
use pocketmine\event\Cancellable;
use solo\sololand\land\Room;

class RoomEnterEvent extends RoomEvent implements Cancellable{
	
	public static $handlerList = null;

	protected $player;
	protected $welcomeMessage;

	public function __construct(Player $player, Room $room, string $welcomeMessage = ""){
		$this->player = $player;
		$this->room = $room;
		$this->welcomeMessage = $welcomeMessage;
	}
	
	public function getWelcomeMessage() : string{
		return $this->welcomeMessage;
	}
	
	public function getPlayer(){
		return $this->player;
	}
	
	public function setWelcomeMessage(string $welcomeMessage){
		$this->welcomeMessage = $welcomeMessage;
	}
}
