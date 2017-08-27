<?php

namespace solo\sitemdb;

use pocketmine\item\Item;

class ItemInfo implements \JsonSerializable{

  protected $name;

  protected $item;

  protected $description;

  public function __construct(Item $item, string $customName = null, string $description = null){
    if($customName !== null){
      $this->name = $customName;
    }else if($item->hasCustomName()){
      $this->name = $item->getCustomName();
    }else{
      $this->name = $item->getName();
    }
    $this->item = Item::get($item->getId(), $item->getDamage(), 1, $item->getCompoundTag());
    $this->description = $description ?? "";
  }

  public function getName() : string{
    return $this->name;
  }

  public function getItem() : Item{
    return Item::get($this->item->getId(), $this->item->getDamage(), 1, $this->item->getCompoundTag());
  }

  public function getDescription() : string{
    return $this->description;
  }

  public function jsonSerialize(){
    return [
      "item" => $this->item,
      "name" => $this->name,
      "description" => $this->description
    ];
  }

  public static function jsonDeserialize(array $data){
    $id = intval($data["item"]["id"]);
    $damage = intval($data["item"]["damage"]);
    $count = intval($data["item"]["count"]);
    $nbt = "";
    if(isset($data["item"]["nbt"])){
      $nbt = $data["item"]["nbt"];
    }else if(isset($data["item"]["nbt_hex"])){
      $nbt = hex2bin($data["item"]["nbt_hex"]); // added in API 3.0.0-ALPHA7
    }
    $item = Item::get($id, $damage, $count, $nbt);
    return new ItemInfo(
      $item,
      $data["name"],
      $data["description"]
    );
  }
}
