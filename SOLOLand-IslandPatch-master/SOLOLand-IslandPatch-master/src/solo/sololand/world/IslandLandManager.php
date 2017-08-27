<?php

namespace solo\sololand\world;

class IslandLandManager extends LandManager{
	
	public function getNextLandId() : int{
		if($this->lastRemember === 1){
			foreach($this->lands as $id => $land){
				if($id > $this->lastRemember){
					$this->lastRemember = $id;
				}
			}
		}
		if(!isset($this->lands[$this->lastRemember])){
			return $this->lastRemember;
		}else if(!isset($this->lands[++$this->lastRemember])){
			return $this->lastRemember;
		}else while(isset($this->lands[++$this->lastRemember])){
			// :)
		}
		return $this->lastRemember;
	}
}