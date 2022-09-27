<?php
if (!function_exists("is_local")) {
    function is_local(): bool
    {
        return config('app.env') === "local";
    }
}