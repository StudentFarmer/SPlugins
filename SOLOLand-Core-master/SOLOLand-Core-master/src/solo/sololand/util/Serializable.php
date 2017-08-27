<?php

namespace solo\sololand\util;

interface Serializable{
	
	public function serialize() : array;
	
	public function unserialize(array $serialized);
	
}