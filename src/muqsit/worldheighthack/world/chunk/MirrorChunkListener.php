<?php

declare(strict_types=1);

namespace muqsit\worldheighthack\world\chunk;

use pocketmine\math\Vector3;
use pocketmine\world\ChunkListener;
use pocketmine\world\format\Chunk;
use pocketmine\world\World;

abstract class MirrorChunkListener implements ChunkListener{

	/** @var World */
	protected $read;

	/** @var World */
	protected $write;

	public function __construct(World $read, World $write){
		$this->read = $read;
		$this->write = $write;
	}

	public function onChunkChanged(int $chunkX, int $chunkZ, Chunk $chunk) : void{
		$this->mirrorChunk($chunkX, $chunkZ, $chunk);
	}

	public function onChunkLoaded(int $chunkX, int $chunkZ, Chunk $chunk) : void{
		if($chunk->isPopulated()){
			$this->mirrorChunk($chunkX, $chunkZ, $chunk);
		}
	}

	public function onChunkUnloaded(int $chunkX, int $chunkZ, Chunk $chunk) : void{
		$this->read->unregisterChunkListener($this, $chunkX, $chunkZ);
	}

	public function onChunkPopulated(int $chunkX, int $chunkZ, Chunk $chunk) : void{
		$this->mirrorChunk($chunkX, $chunkZ, $chunk);
	}

	public function onBlockChanged(Vector3 $block) : void{
	}

	protected function mirrorChunk(int $chunkX, int $chunkZ, Chunk $chunk) : void{
	}
}