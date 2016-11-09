<?php

/**
 * Class mediainfo
 */
class mediainfo
{

	/**
	 * @param $channel
	 * @param string $type
	 * @return mixed
	 */
	public static function fetchChannel($channel, $type = "all")
    {
        return json_decode(mediainfo::syscall('ffprobe -v quiet -print_format json -show_format -show_streams rtmp://localhost/live/' . $channel), TRUE);
    }

	/**
	 * @param $cmd
	 * @return bool|null|string
	 */
	private static function syscall($cmd)
    {
        if ($proc = popen("($cmd)2>&1", "r")) {
            $result = null;
            while (!feof($proc))
                $result .= fgets($proc, 1000);
            pclose($proc);
            return $result;
        } else {
            return false;
        }
    }

	/**
	 * @param $video
	 * @param string $type
	 * @return mixed
	 */
	public static function fetchVideo($video, $type = "all")
    {
        return json_decode(mediainfo::syscall('ffprobe -v quiet -print_format json -show_format -show_streams ' . $video), TRUE);
    }

}
