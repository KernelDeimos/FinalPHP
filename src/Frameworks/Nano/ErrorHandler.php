<?php

namespace FinalPHP\Frameworks\Nano;

use \FinalPHP\L;

class ErrorHandler
{
    /**
     * ErrorHandler provides a single location where errors and warnings
     * are stored, making error-handling more flexible.
     */
    function __construct($config)
    {
        L::AssertStruct($config, self::DEF_Config());

        $this->config = $config;

        $this->logsError = array();
        $this->logsWarn = array();
        $this->logsNote = array();

        $this->fatalCallbacks = array();
    }

    function attach()
    {
        set_error_handler(
            array($this, 'handle_error')
        );
        register_shutdown_function(
            array($this, 'handle_fatal')
        );
    }

    function get_reports()
    {
        return array(
            "errors"   => $this->logsError ,
            "warnings" => $this->logsWarn  ,
            "notices"  => $this->logsNote  );
    }

    function on_fatal($callback)
    {
        $this->fatalCallbacks[] = $callback;
    }

    function handle_error($level, $message, $errfile, $errline)
    {
        $report = self::DEF_Report();
        $report['level']   = $level;
        $report['message'] = $message;
        $report['file']    = $errfile;
        $report['line']    = $errline;

        switch ($level) {
        case E_RECOVERABLE_ERROR:
		case E_USER_ERROR:
            $this->logsError[] = $report;
			break;
		case E_WARNING:
		case E_USER_WARNING:
            $this->logsWarn[] = $report;
			break;
		case E_NOTICE:
		case E_USER_NOTICE:
            $this->logsNote[] = $report;
			break;
        }

        if ($this->config['errors_to_exceptions']) {
            if ($level === E_RECOVERABLE_ERROR || $level === E_USER_ERROR) {
    			throw new ErrorException(
                    $errstr, $errno, 0, $errfile, $errline
                );
            }
        }
    }

    function handle_fatal()
    {
        $reversed = array_reverse($this->fatalCallbacks);
        foreach ($reversed as $callback) {
            $err = error_get_last();
            $callback($err);
        }
    }

    public static function DEF_Config() {
        return L::Struct(
            L::Prop("errors_to_exceptions", "boolean"),
            L::END
        );
    }
    public static function DEF_Report() {
        return L::Struct(
            L::Prop("level", "string"),

            L::Prop("message", "string"),

            L::Prop("file", "string"),
            L::Prop("line", "int"),

            L::END
        );
    }
}