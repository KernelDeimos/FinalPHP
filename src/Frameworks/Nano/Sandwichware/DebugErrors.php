<?php

namespace FinalPHP\Frameworks\Nano\Sandwichware;

class DebugErrors {
    function before_handler($c, $api) {
        $c->set('finalphp.debug', $this);
        $this->userlogs = array();
    }
    function log($message) {
        $this->userlogs[] = $message;
    }
    function dump($message) {
        ob_start();
        var_dump($message);
        $userlog = ob_get_clean();
        $this->userlogs[] = "DUMP>>>\n".trim($userlog)."\n<<<";
    }
    function after_handler($c, $api) {
        $reports = $api->errors->get_reports();

        echo "<!--\n";

        echo <<<LOGO
______ _             _______ _   _ ______ 
|  ___(_)           | | ___ \ | | || ___ \
| |_   _ _ __   __ _| | |_/ / |_| || |_/ /
|  _| | | '_ \ / _` | |  __/|  _  ||  __/ 
| |   | | | | | (_| | | |   | | | || |    
\_|   |_|_| |_|\__,_|_\_|   \_| |_/\_|
LOGO;

        echo "\n\n === ERROR REPORT ===\n\n";
        printf("Error count:  %8d\nWarn count:   %8d\nNotice count: %8d\nUser logs:    %8d\n",
            count($reports['errors']),
            count($reports['warnings']),
            count($reports['notices']),
            count($this->userlogs)
        );
        foreach ($reports as $key => $report) {
            if (count($report) > 0) {
                echo "\n --- " . ucwords($key) . " ---\n";
                foreach ($report as $k => $error) {
                    printf("%8d: ", $k);
                    echo "|".$error['message'] . "| (".$error['file'].":".$error['line'].")\n";
                }
            }
        }
        echo "\n --- User Logs ---\n";
        foreach ($this->userlogs as $k => $userlog) {
            printf("%8s  ", $k);
            echo "|".$userlog."|\n";
        }
        echo "\nEND OF ERROR REPORT -->";
    }

    function fatal_handler($err) {
        echo "<pre>";
        echo <<<LOGO
______ _             _______ _   _ ______ 
|  ___(_)           | | ___ \ | | || ___ \
| |_   _ _ __   __ _| | |_/ / |_| || |_/ /
|  _| | | '_ \ / _` | |  __/|  _  ||  __/ 
| |   | | | | | (_| | | |   | | | || |    
\_|   |_|_| |_|\__,_|_\_|   \_| |_/\_|


LOGO;
        echo "===== Aww, it crashed :( =====\n\n";
        var_dump($err);
        echo "</pre>";
    }
}
