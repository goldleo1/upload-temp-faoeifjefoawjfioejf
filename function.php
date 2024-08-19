<?php
function alert($msg) {
    die("<script>alert('" . $msg . "');</script>");
}
function alert_302($msg, $path) {
    die("<script>alert('" . $msg . "');location.href='". $path ."'</script>");
}
function alert_back($msg) {
    die("<script>alert('" . $msg . "');window.history.back()</script>");
}
?>