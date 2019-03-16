<?php
namespace Presto\Core\Utilities;

use Presto\Core\Traits\Singletonable;

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

    protected $time_start;
    protected $time_before;
    protected $time_current;
    protected $logs = [];

    protected $total_time = "";
    protected $total_memory = 0;

    public function __construct()
    {
        $this->time_start = $this->time_before = $this->time_current = microtime(true);
    }

    /**
     * 先頭に追加
     */
    public function unshift($time_start)
    {
        $this->time_start = $this->time_before = $time_start;
    }


    /**
     * レンダリング
     * @param string $layout
     * @param string $template
     */
    public function render(string $layout="", string $template="")
    {
        // includeしたファイル一覧
        $this->files();

        // ログファイルに書き込む
        $this->logging();

        $layout = empty($layout) ? "html/layouts/empty" : $layout;
        $template = empty($template) ? "html/partials/debugbar" : $template;
        echo view()->layout($layout)->template($template)->render($this->all());
    }


    /**
     * 全部取得
     * @return array[]
     */
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


    public function totalTime() { return $this->total_time; }
    public function totalMemory() { return $this->total_memory; }


    /**
     * Messageログの記録
     * @param string $msg
     * @param array $data
     */
    public function messages(string $msg="", array $data=[])
    {
        $this->record(self::TYPE_MESSAGES, $msg, $data);
    }

    /**
     * Timelineログの記録
     * @param string $msg
     * @param array $data
     */
    public function timelines(string $msg="", array $data=[])
    {
        $this->record(self::TYPE_TIMELINES, $msg, $data);
    }

    /**
     * 異常ログの記録
     * @param string $msg
     * @param array $data
     */
    public function exceptions(string $msg="", array $data=[])
    {
        $this->record(self::TYPE_EXCEPTIONS, $msg, $data);
    }

    /**
     * SQL Queryログの記録
     * @param string $msg
     * @param array $data
     */
    public function queries(string $msg="", array $data=[])
    {
        $this->record(self::TYPE_QUERIES, $msg, $data);
    }

    public function timerstart()
    {
        $this->record("");
    }


    /**
     * 記録
     * @param string $type
     * @param string $msg
     * @param array $data
     */
    protected function record(string $type, string $msg="", array $data=[])
    {
        $this->time_current = microtime(true);

        $row = [];
        $row['memory_usage'] = round(memory_get_usage() / (1024 * 1024), 3);
        $row['memory_usage_peak'] = round(memory_get_peak_usage() / (1024 * 1024), 3);

        $this->total_memory = ($this->total_memory < $row['memory_usage_peak']) ? $row['memory_usage_peak']: $this->total_memory;
        $this->total_time = round($this->time_current - $this->time_start, 4);

        $row['time_total'] = $this->total_time;
        $row['time_execute'] = round($this->time_current - $this->time_before, 4);

        $row['message'] = $msg;
        $row['data'] = $data;

        // 呼び出し元
        $debug_backtrace = debug_backtrace();
        $row['file'] = $debug_backtrace[2]['file'] ?? "";
        $row['line'] = $debug_backtrace[2]['line'] ?? "";

        $this->logs[$type][] = $row;

        $this->time_before = microtime(true);
    }


    /**
     * includeしたファイル一覧
     */
    private function files()
    {
        // includeしたファイル一覧
        foreach (get_included_files() as $file)
        {
            $this->record(self::TYPE_FILES, $file);
        }
    }


    /**
     * ログへの書き込み
     */
    private function logging()
    {
        // ファイルに書き込む
        $directory = storage_path("debugbar/" . date("Ymd/H/"));

        if(!file_exists($directory))
        {
            mkdir($directory, 0777, TRUE);
        }

        $filename = $directory.date("Ymd-His-").uniqid() . ".json";
        file_put_contents($filename, json_encode($this->logs, JSON_UNESCAPED_UNICODE));
    }

}