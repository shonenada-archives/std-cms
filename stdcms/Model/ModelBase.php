<?php

/**
 * 模型基础类
 * @author shonenada
 *
 */

namespace Model;

class ModelBase {

    static protected $conn = 'master';

    // 永久化对象
    public function save($flush=true) {
        static::em()->persist($this);
        if ($flush) {
            self::flush();
        }
    }

    // 移除对象
    public function remove($flush=true) {
        static::em()->remove($this);
        if ($flush) {
            self::flush();
        }
    }

    // 从 array 中赋值 property
    public function populateFromArray($array=array()) {
        foreach($array as $key => $value){
            if ($key != 'id' && property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
        return $this;
    }

    static public function dump($input) {
        return \Doctrine\Common\Util\Debug::dump($input);
    }

    static public function flush(){
        static::em()->flush();
    }

    static public function find($id) {
        return static::em()->find(get_called_class(), $id);
    }

    static public function findBy($criteria, $order_by=null, $limit=null, $offset=null) {
        return static::query()->findBy($criteria, $order_by, $limit, $offset);
    }

    static public function findOneBy($array, $order_by=null) {
        return static::query()->findOneBy($array, $order_by);
    }

    static public function query()
    {
        return static::em()->getRepository(get_called_class());
    }

    static public function em() {
        return ORMManager::get(self::$conn);
    }

    static public function paginate($page, $pagesize) {
        $dql = sprintf(
            'SELECT n FROM %s n '.
            'WHERE n.is_deleted = 0 ',
            get_called_class()
        );
        $query = static::em()->createQuery($dql)->setFirstResult($pagesize*($page-1))->setMaxResults($pagesize);
        $pager = new \Doctrine\ORM\Tools\Pagination\Paginator($query);
        return $pager;
    }

    static public function all($asc=true) {
        $dql = sprintf(
            'SELECT n FROM %s n '.
            'WHERE n.is_deleted = 0 '.
            'ORDER BY n.id %s ',
            get_called_class(),
            $asc ? 'ASC' : 'DESC'
        );
        $query = static::em()->createQuery($dql);
        return $query->useQueryCache(true)->getResult();
    }

    static public function getList($page=1, $pagesize=20, $order_by='id', $asc=false) {
        $dql = sprintf(
            'SELECT n FROM %s n '.
            'ORDER BY n.%s %s', 
            get_called_class(),
            $order_by,
            $asc ? 'ASC' : 'DESC'
        );
        $query = static::em()->createQuery($dql)->setMaxResults($pagesize)->setFirstResult($pagesize*($page-1));
        return $query->useQueryCache(false)->getResult();
    }

    static public function countAll() {
        $dql = sprintf('SELECT count(n) FROM %s n ', get_called_class());
        $query = static::em()->createQuery($dql);
        return $query->useQueryCache(false)->getOneOrNullResult();
    }

    static public function getRandom() {
        $count = self::countAll();
        $count = array_shift($count);
        $random_id = mt_rand(0, $count - 1);
        $dql = sprintf('SELECT n FROM %s n ', get_called_class());
        $query = static::em()->createQuery($dql)->setMaxResults(1)->setFirstResult($random_id);
        return $query->useQueryCache(false)->getOneOrNullResult();
    }

    static public function allWithDeleted() {
        $dql = sprintf(
            'SELECT n FROM %s n',
            get_called_class()
        );
        $query = static::em()->createQuery($dql);
        return $query->useQueryCache(true)->getResult();
    }
}


class ORMManager {


    static public $conn = array();
    static public $managers = array();
    static public $config = null;
    static public $eventManager = null;

    public static function init() {
        if (!file_exists(STDROOT . 'config/database.conf.php'))
            exit('Database config file not found!');

        $connections = require(STDROOT . 'config/database.conf.php');
        foreach($connections as $name => $connInfo) {
            static::$conn[$name] = $connInfo;
        }

        static::$config = new \Doctrine\ORM\Configuration();
        static::$eventManager = new \Doctrine\Common\EventManager();

        $driver = static::$config->newDefaultAnnotationDriver(array(STDROOT . "Model/"));

        static::$config->setMetadataDriverImpl($driver);
        static::$config->setProxyDir(STDROOT. 'cache/');
        static::$config->setProxyNamespace("DoctrineProxy");

        if (extension_loaded('wincache')) {
            static::$config->setMetadataCacheImpl(new \Doctrine\Common\Cache\WinCache());
            static::$config->setQueryCacheImpl(new \Doctrine\Common\Cache\WinCache());
            static::$config->setResultCacheImpl(new \Doctrine\Common\Cache\WinCache());
        } else if (extension_loaded('apc')) {
            static::$config->setMetadataCacheImpl(new \Doctrine\Common\Cache\ApcCache());
            static::$config->setQueryCacheImpl(new \Doctrine\Common\Cache\ApcCache());
            static::$config->setResultCacheImpl(new \Doctrine\Common\Cache\ApcCache());
        } else {
            static::$config->setMetadataCacheImpl(new \Doctrine\Common\Cache\ArrayCache());
        }
    }

    static public function get($name) {
        assert(isset(static::$conn[$name]));

        if (!isset(self::$managers[$name])) {
            self::$managers[$name] = \Doctrine\ORM\EntityManager::create(
                self::$conn[$name], static::$config, static::$eventManager
            );
        }

        return self::$managers[$name];
    }

    static public function getEntityManager() {
        return self::$entityManager;
    }

} ORMManager::init();