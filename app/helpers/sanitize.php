<?php
function clean($v) {
    return htmlspecialchars(trim($v), ENT_QUOTES, 'UTF-8');
}
