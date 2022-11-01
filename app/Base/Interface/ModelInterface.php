<?php

namespace FarhangNegari\App\Base\Interface;

interface ModelInterface
{
    public function  connect($setting);
    public function  find($id);
    // public function  first();
    public function  byId($id);
    public function  get();
    public function  where($where);
    public function  delete();
    public function  insert($value);
    public function  update($value);
    public function  bginTransaction();
    public function  commit();
    public function  rollback();
    public function  paginate($request);
}