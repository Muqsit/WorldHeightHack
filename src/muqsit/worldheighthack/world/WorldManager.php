<?php

declare(strict_types=1);

namespace muqsit\worldheighthack\world;

use muqsit\worldheighthack\generator\VoidGenerator;
use muqsit\worldheighthack\Loader;
use pocketmine\world\Position;
use pocketmine\world\World;
use pocketmine\world\WorldCreationOptions;

final class WorldManager{

	private static function getY(World $world) : int{
		$data = explode(".", $world->getFolderName());
		if(isset($data[1])){
			return (int) $data[1];
		}
		return 0;
	}

	/** @var WorldInstance[] */
	private $worlds = [];

	/** @var int[] */
	private $worlds_y = [];

	public function __construct(Loader $loader){
		$loader->getServer()->getPluginManager()->registerEvents(new WorldListener($this), $loader);
		foreach($loader->getServer()->getWorldManager()->getWorlds() as $world){
			$this->onWorldLoad($world);
			$instance = $this->get($world);
			foreach($world->getLoadedChunks() as $chunkHash => $chunk){
                World::getXZ($chunkHash, $chunkX, $chunkZ);
				$instance->onChunkLoad($chunkX, $chunkZ);
			}
		}

		for($i = 1; $i <= 4; ++$i){
			if(!$loader->getServer()->getWorldManager()->loadWorld("world." . $i)){
				$loader->getServer()->getWorldManager()->generateWorld("world." . $i, WorldCreationOptions::create());
			}
		}
	}

	public function onWorldLoad(World $world) : void{
		$this->worlds[$id = $world->getId()] = $instance = new WorldInstance($world, $y = self::getY($world));
		$this->worlds_y[$y] = $id;

		$above = $this->getAt($y + 1);
		if($above !== null){
			$above->onWorldBelowLoad($instance);
			$instance->onWorldAboveLoad($above);
		}

		$below = $this->getAt($y - 1);
		if($below !== null){
			$below->onWorldAboveLoad($instance);
			$instance->onWorldBelowLoad($below);
		}
	}

	public function onWorldUnload(World $world) : void{
		if(isset($this->worlds[$id = $world->getId()])){
			$instance = $this->worlds[$id];
			$y = $instance->getY();

			$above = $this->getAt($y + 1);
			if($above !== null){
				$above->onWorldBelowUnload($instance);
			}

			$below = $this->getAt($y - 1);
			if($below !== null){
				$below->onWorldAboveUnload($instance);
			}

			unset($this->worlds_y[$y], $this->worlds[$id]);
		}
	}

	public function get(World $world) : WorldInstance{
		return $this->worlds[$world->getId()];
	}

	public function getAt(int $y) : ?WorldInstance{
		return $this->worlds[$this->worlds_y[$y] ?? -1] ?? null;
	}

	public function translateCoordinates(Position $position) : Position{
		$instance = $this->get($position->world);
		$worldY = $instance->getY();

		$y = $position->getFloorY() >> 4;
		if($y >= (16 - 4)){
			$newWorldY = $worldY + 1;
			$new_y = 64 + ($position->y - ((16 - 4) << 4));
		}elseif($y < 4){
			$newWorldY = $worldY - 1;
			$new_y = 128 - ($position->y - (4 << 4));
		}else{
			return $position;
		}

		return new Position($position->x, $new_y, $position->z, ($this->getAt($newWorldY) ?? $instance)->getWorld());
	}
}