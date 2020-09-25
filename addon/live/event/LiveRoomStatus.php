<?php
/**
 * KirySaaS--------||bai T o o Y ||
 * =========================================================
 * ----------------------------------------------
 * User Mack Qin
 * Copy right 2019-2029 kiry 保留所有权利。
 * ----------------------------------------------
 * =========================================================
 */



namespace addon\live\event;

use addon\live\model\Room;

/**
 * 轮询更新直播间状态
 */
class LiveRoomStatus
{

    /**
     * 轮询更新直播间状态
     * 
     * @return multitype:number unknown
     */
	public function handle($param)
	{
        $room = new Room();
        $room->updateRoomStatus();
	}
}