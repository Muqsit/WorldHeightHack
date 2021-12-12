<?php

declare(strict_types=1);

namespace muqsit\worldheighthack\world;

use muqsit\worldheighthack\world\chunk\AboveChunkUpdater;
use muqsit\worldheighthack\world\chunk\BelowChunkUpdater;
use pocketmine\world\World;

final class WorldInstance{

	/** @var World */
	private $world;

	/** @var int */
	private $y;

	/** @var AboveChunkUpdater|null */
	private $above_listener;

	/** @var BelowChunkUpdater|null */
	private $below_listener;

	public function __construct(World $world, int $y){
		$this->world = $world;
		$this->y = $y;
	}

	public function getY() : int{
		return $this->y;
	}

	public function getWorld() : World{
		return $this->world;
	}

	public function onWorldBelowLoad(WorldInstance $instance) : void{
		$this->below_listener = new BelowChunkUpdater($this->world, $instance->world);
		foreach($this->world->getLoadedChunks() as $chunkHash => $chunk){
            World::getXZ($chunkHash, $chunkX, $chunkZ);
			$this->registerBelow($chunkX, $chunkZ);
		}
	}

	public function onWorldAboveLoad(WorldInstance $instance) : void{
		$this->above_listener = new AboveChunkUpdater($this->world, $instance->world);
		foreach($this->world->getLoadedChunks() as $chunkHash => $chunk){
            World::getXZ($chunkHash, $chunkX, $chunkZ);
			$this->registerAbove($chunkX, $chunkZ);
		}
	}

	public function onWorldBelowUnload(WorldInstance $instance) : void{
		$this->below_listener = null;
	}

	public function onWorldAboveUnload(WorldInstance $instance) : void{
		$this->above_listener = null;
	}

	public function onChunkLoad(int $chunkX, int $chunkZ) : void{
		$this->registerAbove($chunkX, $chunkZ);
		$this->registerBelow($chunkX, $chunkZ);
	}

	private function registerAbove(int $chunkX, int $chunkZ) : void{
		if($this->above_listener !== null){
			$this->world->registerChunkListener($this->above_listener, $chunkX, $chunkZ);
			$this->above_listener->onChunkLoaded($chunkX, $chunkZ, $this->world->getChunk($chunkX, $chunkZ));
		}
	}

	private function registerBelow(int $chunkX, int $chunkZ) : void{
		if($this->below_listener !== null){
			$this->world->registerChunkListener($this->below_listener, $chunkX, $chunkZ);
			$this->below_listener->onChunkLoaded($chunkX, $chunkZ, $this->world->getChunk($chunkX, $chunkZ));
		}
	}

	public function onChunkUnload(int $chunkX, int $chunkZ) : void{
	}
}