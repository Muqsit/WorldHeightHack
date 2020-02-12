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

	public function onChunkChanged(Chunk $chunk) : void{
		$this->mirrorChunk($chunk);
	}

	public function onChunkLoaded(Chunk $chunk) : void{
		if($chunk->isPopulated()){
			$this->mirrorChunk($chunk);
		}
	}

	public function onChunkUnloaded(Chunk $chunk) : void{
		$this->read->unregisterChunkListener($this, $chunk->getX(), $chunk->getZ());
	}

	public function onChunkPopulated(Chunk $chunk) : void{
		$this->mirrorChunk($chunk);
	}

	public function onBlockChanged(Vector3 $block) : void{
	}

	protected function mirrorChunk(Chunk $chunk) : void{
	}
}