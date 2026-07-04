<?php
// GrandMaster Analysis Portal - runtime configuration.

// Engine settings
$engine_path = "/opt/engine/analyze.sh";
$default_depth = 12;
// Hard wall-clock cap on an engine run so a hung/blocking analysis can only
// tie up an Apache worker for a bounded window (seconds).
$engine_timeout = 180;

// Weekly standings report, produced by the scheduled report job.
$report_json = "/opt/chess/report/weekly_report.json";
$report_file = "/opt/chess/report/weekly_report.txt";

// Local read-only DB account used by the portal (no shell, app-scoped).
$db_host = "127.0.0.1";
$db_name = "chessdb";
$db_user = "portal_ro";
$db_pass = "ro-9f2a1c7b";
?>
