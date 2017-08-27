<?php

namespace solo\sololand\event\room;

use pocketmine\event\Event;
use solo\sololand\land\Room;

class RoomEvent extends Event{
	protected $room;
	
	public function getRoom(){
		return $this->room;
	}
}
