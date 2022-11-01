<?php
interface CachInterface
{
    public function  set($key);
    public function  has($key);
    public function  get($key);
}