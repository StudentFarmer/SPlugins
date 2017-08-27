<?php

namespace solo\sololand\event\world;

use pocketmine\event\Event;
use solo\sololand\world\World;

class WorldEvent extends Event{
	protected $world;
	
	public function getWorld() : World{
		return $this->world;
	}
}