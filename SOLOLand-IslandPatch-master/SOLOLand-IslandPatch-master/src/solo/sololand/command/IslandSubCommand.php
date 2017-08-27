<?php

namespace solo\sololand\command;

abstract class IslandSubCommand extends SubCommand{
	
	protected $world;
	
	public function getWorld(){
		return $this->world;
	}
}