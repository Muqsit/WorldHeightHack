<?php

declare(strict_types=1);

namespace muqsit\worldheighthack\world\chunk;

use pocketmine\math\Vector3;
use pocketmine\world\format\Chunk;
use pocketmine\world\format\io\FastChunkSerializer;

// Read from above chunk, write to below chunk
final class BelowChunkUpdater extends MirrorChunkListener{

	public function onBlockChanged(Vector3 $block) : void{
		if($block->y >= 64 && $block->y < 128){
			$this->write->setBlock(new Vector3($block->x, 192 + ($block->y - 64), $block->z), $this->read->getBlock($block), false);
		}
	}

	protected function mirrorChunk(Chunk $chunk) : void{
		$target = $this->write->getChunk($chunk->getX(), $chunk->getZ(), true);
		if($target !== null){
			$copy = FastChunkSerializer::deserialize(FastChunkSerializer::serialize($chunk));
			for($y = 64 >> 4; $y < 128 >> 4; ++$y){
				$target->setSubChunk(12 + ($y - (64 >> 4)), $copy->getSubChunk($y));
			}
		}
	}
}