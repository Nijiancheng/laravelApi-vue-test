<?php
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\LineFormatter;

class BLog
{
    const LOG_EMERGENCY = 'EMERGENCY';  //紧急状况，比如系统挂掉
    const LOG_ALERT     = 'ALERT';      //需要立即采取行动的问题，比如整站宕掉，数据库异常等，
    const LOG_CRITICAL  = 'CRITICAL';   //严重问题，比如：应用组件无效，意料之外的异常
    const LOG_ERROR     = 'ERROR';      //运行时错误，不需要立即处理但需要被记录和监控
    const LOG_WARNING   = 'WARNING';    //警告但不是错误，比如使用了被废弃的API
    const LOG_NOTICE    = 'NOTICE';     //普通但值得注意的事件
    const LOG_INFO      = 'INFO';       //感兴趣的事件，比如登录、退出
    const LOG_DEBUG     = 'DEBUG';      //详细的调试信息
    const LOG_DATABASE  = 'DATABASE';   //数据库查询
    const LOG_PAY       = 'PAY';        //充值查询
    const LOG_VIDEO     = 'VIDEO';      //充值查询

    private static $loggers = array();

    // 获取一个实例
    public static function getLogger($type = self::LOG_ERROR, $day = 30)
    {
        if (empty(self::$loggers[$type])) {
            self::$loggers[$type] = new Logger($type);
            $handler = (new RotatingFileHandler(storage_path("logs/".$type.".log"), $day))
                ->setFormatter(new LineFormatter(null, null, true, true));
            self::$loggers[$type]->pushHandler($handler);
        }

        $log = self::$loggers[$type];
        return $log;
    }

    public static function write($str,$name=BLog::LOG_NOTICE,$day=30){
        if(!is_string($str)) $str = json_encode($str);
        return \BLog::getLogger($name)->info($str);
    }

    public static function db($str){
        return self::write($str,BLog::LOG_DATABASE,5);
    }
    public static function debug($str){
        return self::write($str,BLog::LOG_DEBUG);
    }
    public static function info($str){
        return self::write($str,BLog::LOG_INFO);
    }
    public static function error($str){
        return self::write($str,BLog::LOG_ERROR);
    }
    public static function alert($str){
        return self::write($str,BLog::LOG_ALERT);
    }
    public static function emergency($str){
        return self::write($str,BLog::LOG_EMERGENCY);
    }
    public static function critical($str){
        return self::write($str,BLog::LOG_CRITICAL);
    }
    public static function warning($str){
        return self::write($str,BLog::LOG_WARNING);
    }
    public static function notice($str){
        return self::write($str,BLog::LOG_NOTICE);
    }
    public static function pay($str){
        return self::write($str,BLog::LOG_PAY);
    }
    public static function video($str){
        return self::write($str,BLog::LOG_VIDEO,5);
    }
}
