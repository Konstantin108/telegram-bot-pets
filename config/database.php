<?php

return [
    "connection" => getenv("DB_CONNECTION"),
    "host" => getenv("DB_HOST"),
    "database" => getenv("DB_DATABASE"),
    "username" => getenv("DB_USERNAME"),
    "password" => getenv("DB_PASSWORD")
];