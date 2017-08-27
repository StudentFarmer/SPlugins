<?php

namespace solo\sololand\event\land;

use pocketmine\event\Event;
use solo\sololand\land\Land;

abstract class LandEvent extends Event{
	protected $land;
	
	public function getLand() : Land{
		return $this->land;
	}
}
