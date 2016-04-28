<?php
class Hashing
{
    static function hash($value)
    {
        return sha1($value);
    }
}
?>