<?php

declare(strict_types=1);

namespace muqsit\worldheighthack\world\chunk;

use pocketmine\math\Vector3;
use pocketmine\world\format\Chunk;
use pocketmine\world\format\io\FastChunkSerializer;

// Read from below chunk, write to above chunk
final class AboveChunkUpdater extends MirrorChunkListener{

	public function onBlockChanged(Vector3 $block) : void{
		if($block->y >= 128 && $block->y < 192){
			$this->write->setBlock(new Vector3($block->x, $block->y - 128, $block->z), $this->read->getBlock($block), false);
		}
	}

	protected function mirrorChunk(Chunk $chunk) : void{
		$target = $this->write->getChunk($chunk->getX(), $chunk->getZ(), true);
		if($target !== null){
			$copy = FastChunkSerializer::deserialize(FastChunkSerializer::serialize($chunk));
			for($y = 128 >> 4; $y < 192 >> 4; ++$y){
				$target->setSubChunk($y - (128 >> 4), $copy->getSubChunk($y));
			}
		}
	}
}