<?php
function open_cache_connection()
{
    $memcache_obj = new Memcache;
    $connection = $memcache_obj->connect('localhost', 11211);
    if ($connection) {
        Global_scope::$memcache_connection = $memcache_obj;
        return $memcache_obj;
    } else {
        return false;
    }
}

function close_cache_connection(&$connect_handle)
{
    if ($connect_handle instanceof Memcache) {
        $connect_handle->close();

    }
    $connect_handle = null;
}

function check_cache_connection(&$connect_handle)
{
    if ((!$connect_handle) || (!$connect_handle->getVersion())) {
        $connect_handle = open_cache_connection();
    }
    return $connect_handle;
}

function write_to_cache($key, $data, $expiration = 0, $unique_key = false, $memcache_flags = false)
{
    Global_scope::$memcache_connection = check_cache_connection(Global_scope::$memcache_connection);

    if (Global_scope::$memcache_connection) {
        if ($unique_key) {
            Global_scope::$memcache_connection->add($key, $data, $memcache_flags ?? false, $expiration ?? 0);
        } else {
            Global_scope::$memcache_connection->set($key, $data, $memcache_flags ?? false, $expiration ?? 0);
        }
        return true;
    } else {
        return false;
    }
}

function read_from_cache($key, callable $refresher = null, $expiration = false, $unique_key = false, $memcache_flags = false): Mixed
{
    $result = '';
    Global_scope::$memcache_connection = check_cache_connection(Global_scope::$memcache_connection);
    if (Global_scope::$memcache_connection) {
        $result = Global_scope::$memcache_connection->get($key);
        if (($result === false) && ($refresher)) {
            $result = $refresher();
            write_to_cache($key, $result, $expiration, $unique_key, $memcache_flags);
        }
    } else {
        return false;
    }
    return $result;
}

function remove_from_cache($key)
{
    $result = false;
    Global_scope::$memcache_connection = check_cache_connection(Global_scope::$memcache_connection);
    if (Global_scope::$memcache_connection) {
        $result = Global_scope::$memcache_connection->delete($key);
    }
    return $result;
}