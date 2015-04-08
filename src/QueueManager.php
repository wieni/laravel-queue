<?php namespace Wieni\Queue;

use DB;

class QueueManager
{
    public function addFirst($queueId, $entityId, $entityType)
    {
        $firstItem = DB::table('queue')->where('queue_id', $queueId)->orderBy('weight')->first();
        $weight = $firstItem ? (int) $firstItem->weight - 1 : 0;

        $this->createQueueItem($queueId, $entityId, $entityType, $weight);
    }

    public function addLast($queueId, $entityId, $entityType)
    {
        $lastItem = DB::table('queue')->where('queue_id', $queueId)->orderBy('weight', 'desc')->first();
        $weight = $lastItem ? (int) $lastItem->weight + 1 : 0;

        $this->createQueueItem($queueId, $entityId, $entityType, $weight);
    }

    public function update($queueId, array $queue)
    {
        foreach ($queue as $index => $queueItem) {
            $this->updateQueueItem($queueId, $queueItem['entity_id'], $queueItem['entity_type'], $index);
        }
    }

    public function removeQueueItem($queueId, $entityId, $entityType)
    {
        DB::table('queue')
            ->where('queue_id', $queueId)
            ->where('entity_id', $entityId)
            ->where('entity_type', $entityType)
            ->delete();
    }

    public function queueItemExists($queueId, $entityId, $entityType)
    {
        $item = DB::table('queue')
            ->where('queue_id', $queueId)
            ->where('entity_id', $entityId)
            ->where('entity_type', $entityType)
            ->first();

        return !is_null($item);
    }

    protected function createQueueItem($queueId, $entityId, $entityType, $weight)
    {
        DB::table('queue')
            ->insert([
                'queue_id' => $queueId,
                'entity_id' => $entityId,
                'entity_type' => $entityType,
                'weight' => $weight
            ]);
    }

    protected function updateQueueItem($queueId, $entityId, $entityType, $weight)
    {
        DB::table('queue')
            ->where('queue_id', $queueId)
            ->where('entity_id', $entityId)
            ->where('entity_type', $entityType)
            ->update(['weight' => $weight]);
    }
}
