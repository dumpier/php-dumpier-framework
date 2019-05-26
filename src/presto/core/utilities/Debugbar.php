<?php
namespace Presto\Core\Utilities;

use Presto\Core\Traits\Singletonable;
use Presto\Core\Views\View;

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

    const LAYOUT = "html/layouts/empty";
    const TEMPLATE = "html/partials/debugbar";

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

        $layout = empty($layout) ? self::LAYOUT : $layout;
        $template = empty($template) ? self::TEMPLATE : $template;
        echo View::instance()->layout($layout)->template($template)->render($this->all());
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
    public function timerstart() { $this->record(""); }

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
        $row['memory_usage'] = memory_get_usage();
        $row['memory_usage_peak'] = memory_get_usage();

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
    public function logging()
    {
        // ファイルに書き込む
        $directory = Pather::instance()->storage("debugbar/" . date("Y/m/d/"));

        if(!file_exists($directory))
        {
            mkdir($directory, 0777, TRUE);
        }

        $filename = $directory.date("Ymd-H") . ".json";
        file_put_contents($filename, json_encode($this->logs, JSON_UNESCAPED_UNICODE). PHP_EOL, FILE_APPEND);
    }

    // --------------------------------------------------------
    // html
    // --------------------------------------------------------
    public function toHtml(array $logs)
    {
        $html = "";
        $html .= "<table class='table table-hover table-striped'>";
        $html .= "<tr>";
        $html .= "<th>Total time</th><th>Execute time</th><th>Memory / Peak</th><th>Message</th><th>File</th>";
        $html .= "</tr>";

        foreach ($logs as $type=>$log)
        {
            $html .= $this->logToHtml($log);
        }

        $html .= "</table>";
        return $html;
    }

    public function logToHtml(array $log)
    {
        $html = "";

        foreach ($log as $key=>$row)
        {
            $html .= "<tr>";
            $html .= "<td>{$row['time_total']}</td>";
            $html .= "<td>{$row['time_execute']}</td>";
            $html .= "<td>" . util()->unit()->auto( $row['memory_usage']) . "&nbsp;/&nbsp;" . util()->unit()->auto( $row['memory_usage_peak']) ."</td>";
            $html .= "<td>".$this->messageToHtml($row)."</td>";
            $html .= "<td>{$row['file']}&nbsp;#{{$row['line']}}</td>";
            $html .= "</tr>";
        }

        return $html;
    }

    private function messageToHtml(array $row)
    {
        $html = $row['message'];
        if (!empty($row['data']))
        {
            $html .= "<a class='text-info' href='javascript: void(0);' onmouseover=\"$('div.debugbar-data-wrap').hide(); $(this).next('div.debugbar-data-wrap').show();\" onmouseout=\"$(this).next('div.debugbar-data-wrap').hide();\">View</a>";
            $html .= "<div class=debugbar-data-wrap><div class=debugbar-data-popup>" . print_r($row['data'], TRUE) . "</div></div>";
        }

        return $html;
    }
    // --------------------------------------------------------

}