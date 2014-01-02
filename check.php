<?php
require "autoload.php";
use OpenCloud\Compute\Constants\ServerState;

function checkaction($response, $waiting) {
    $callback = function($response) {
        if (!empty($response->error)) {
            var_dump($response->error);
            exit;
        } else {
            echo sprintf(
                "\rWaiting on %s/%-12s %4s%%",
                $response->name(),
                $response->status(),
                isset($response->progress) ? $response->progress : 0
            );
        }
    };

    $response->waitFor($waiting, 600, $callback);
}
?>
