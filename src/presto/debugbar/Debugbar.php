<?php
namespace Presto\Debugbar;

use Presto\Traits\Singletonable;

class Debugbar
{
    use Singletonable;

    const TYPE_MESSAGES = "messages";
    const TYPE_TIMELINES = "timelines";
    const TYPE_EXCEPTIONS = "exceptions";
    const TYPE_QUERIES = "queries";
    const TYPE_LOGS = "logs";
    const TYPE_VIEWS = "views";
    const TYPE_SESSIONS = "sessions";
    const TYPE_REQUESTS = "requests";
    const TYPE_RESPONSES = "responses";
    const TYPE_FILES = "files";
    const TYPE_ROUTES = "routes";
    const TYPE_CONFIGS = "configs";
    const TYPE_AUTH = "auth";
    const TYPE_GATE = "gate";

    private $time_start;
    private $time_before;
    private $time_current;
    private $logs = [];

    protected function init()
    {
        $time = microtime(true);
        $this->time_start = $time;
        $this->time_before = $time;
        $this->time_current = $time;
    }


    public function all()
    {
        return ['logs'=>$this->logs];
    }

    /**
     * ログ件数の取得
     * @param string $type
     * @return number
     */
    public function count(string $type)
    {
        if(empty($this->logs[$type]))
        {
            return 0;
        }

        return count($this->logs[$type]);
    }


    /**
     * Messageログの記録
     * @param string $msg
     * @param array $data
     */
    public function messages(string $msg="", array $data=[])
    {
        $this->recording(self::TYPE_MESSAGES, $msg, $data);
    }

    /**
     * Timelineログの記録
     * @param string $msg
     * @param array $data
     */
    public function timelines(string $msg="", array $data=[])
    {
        $this->recording(self::TYPE_TIMELINES, $msg, $data);
    }

    /**
     * 異常ログの記録
     * @param string $msg
     * @param array $data
     */
    public function exceptions(string $msg="", array $data=[])
    {
        $this->recording(self::TYPE_EXCEPTIONS, $msg, $data);
    }

    /**
     * SQL Queryログの記録
     * @param string $msg
     * @param array $data
     */
    public function queries(string $msg="", array $data=[])
    {
        $this->recording(self::TYPE_QUERIES, $msg, $data);
    }

    public function timerstart()
    {
        $this->recording(0);
    }

    public function recording(string $type, string $msg="", array $data=[])
    {
        $this->time_current = microtime(true);

        $row = [];
        $row['memory_usage'] = round(memory_get_usage() / (1024 * 1024), 3);
        $row['memory_usage_peak'] = round(memory_get_peak_usage() / (1024 * 1024), 3);

        $row['time_total'] = round($this->time_current - $this->time_start, 4);
        $row['time_execute'] = round($this->time_current - $this->time_before, 4);

        $row['message'] = $msg;
        $row['data'] = $data;

        // 呼び出し元
        $debug_backtrace = debug_backtrace();
        $row['file'] = $debug_backtrace[2]['file'];
        $row['line'] = $debug_backtrace[2]['line'];

        $this->logs[$type][] = $row;

        $this->time_before = microtime(true);
    }

}