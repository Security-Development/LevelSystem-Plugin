<?php

/**
 * @name LevelSystem
 * @main src\LevelSystem
 * @author Byte
 * @version 1.0.0
 * @api 3.0.0
 */

 namespace src;

 use pocketmine\ {
   utils\Config
 };

 date_default_timezone_set('asia/seoul');

 trait LevelMethod {

   public function isBoster(string $name) : bool {
     if($this->get($name)['boster']['time'] > 0)
      return true;
     else
      return false;
   }

   public function setBoster(string $name, string $time, int $number) : void {
     if(!$this->isKey($name)) {
       trigger_error("Function setBosterExp() 1 Argument value is null", E_USER_ERROR);
     } else {
       $arr = $this->get($name);
       $arr['boster'] = ['time' => $time, 'multiples' => $number];

       $this->set($name, $arr);

       unset($arr);//$arr 메모리 해제
     }
   }

   public function getBoster(string $name) : array {
     if(!$this->isKey($name))
      return array_fill(0, 2, 'Not Found');
     else
      return ((array)$this->get($name)['boster']);//key : [time, number]
   }

   public function getLevel(string $name) : int | string {
     if(!$this->isKey($name))
      return "Not Found";
     else
      return (int) $this->get($name)['level'];
   }

   public function setLevel(string $name, int $level) : void {

    if(!$this->isKey($name)) {
      trigger_error("Function setLevel() 1 Argument value is null", E_USER_ERROR);
    } else{
      $arr = $this->get($name);
      $arr['level'] = $level;
      $this->set($name, $arr);
      unset($arr);
    }
   }

   public function getExp(string $name) : int | string {
     if(!$this->isKey($name))
      return "Not Found";
     else
      return (int)$this->get($name)['exp'];
   }

   public function setExp(string $name, int $exp) : void {
     if(!$this->isKey($name)) {
       trigger_error("Function setExp() 1 Argument value is null", E_USER_ERROR);
     } else{
       $arr = $this->get($name);
       $arr['exp'] = $exp;
       $this->set($name, $arr);
       unset($arr);
     }
  }

  public function addLevel(string $name, int $level) : void {
    if($level <= 0)
      trigger_error("Function addLevel() 1 Argument value must be greater than 0.", E_USER_ERROR);
    else
      $this->setLevel($name, ((int)$this->getLevel($name)) + $level);
  }

  public function addExp(string $name, int $exp) : void {
    if($exp <= 0) {
      trigger_error("Function addExp() 1 Argument value must be greater than 0.", E_USER_ERROR);
    } else {
      $x = $this->getExp($name) + $exp;
      $level = floor($x) / $this->getMaxExp($name);
      $y = abs(($x) - ($this->getMaxExp($name) * ((int) $level)));
      $this->setLevel($name, $this->getLevel($name) + ((int)$level));
      $this->setExp($name, $this->getMultiples($name) != 0 ? $y * $this->getMultiples($name) : $y);
      unset($level);
      unset($x);
      unset($y);
    }
  }

  public function subLevel(string $name, int $level) : void {
    if($level > 0)
      trigger_error("Function subLevel() 1 Argument value must be less than 0.", E_USER_ERROR);
    else
      $this->setLevel($name, $this->getLevel($name) - $level);
  }

  public function subExp(string $name, int $exp) : void {
    if($exp <= 0) {
      trigger_error("Function subExp() 1 Argument value must be greater than 0.", E_USER_ERROR);
    } else {
      $x = $this->getExp($name) - $exp;
      $y = $this->getMaxExp($name) - abs($x);
      $level = floor(($x < 0 ? abs(($x - 100) - ($x / 100)) : abs($x))) / $this->getMaxExp($name);
      $this->setLevel($name, $this->getLevel($name) - ((int)$level));
      $this->setExp($name, abs($x < 0 ?  ($y >= $this->getMaxExp($name) ? $y - ($this->getMaxExp($name) * ((int) $level)) : $y) : $x));
      unset($x);
      unset($y);
      unset($level);

    }
  }

  public function getMaxExp(string $name) : int | string {
    if(!$this->isKey($name))
     return "Not Found";
    else
     return (int)$this->get($name)['maxExp'];
  }

  public function setMaxExp(string $name, int $exp) : void {
    if($exp >= $this->getExp($name) || $exp < 0)
    {
      trigger_error("Function setMaxExp() 1 Argument value must be less than 0 and greater than getExp() value.", E_USER_ERROR);
    } else {
      $arr = $this->get($exp);
      $arr['maxExp'] = $level;
      $this->set($name, $arr);
      unset($arr);
    }
  }

  public function addMaxExp(string $name, int $exp) : void {
    if($exp <= 0)
      trigger_error("Function addMaxExp() 1 Argument value must be greater than 0.", E_USER_ERROR);
    else
      $this->setMaxExp($name, $this->getMaxExp($name) + $exp);
  }

  public function subMaxExp(string $name, int $exp) : void {
    if($exp > 0)
      trigger_error("Function subMaxExp() 1 Argument value must be less than 0.", E_USER_ERROR);
    else
      $this->setMaxExp($name, $this->getMaxExp($name) - $exp);
  }

  public function setTime(string $name, string $time) : void {
    if(strtotime($time) < 0)
    {
      trigger_error("Function setTime() 1 Argument value must be in the future than the current time.", E_USER_ERROR);
    } else {
      $arr = $this->get($name);
      $arr['boster']['time'] = $time;
      $this->set($name, json_encode($arr, JSON_UNESCAPED_UNICODE));
      unset($arr);
    }
  }

  public function getTime(string $name) : string {
    if(!$this->isKey($name))
      return "Not Found";
    else
      return $this->get($name)['boster']['time'];
  }

  public function getExpiryTime(string $name) : string {
    if(!$this->isKey($name))
    {
      return "Not Found";
    }else {
      $time = strtotime($this->getBoster($name)['time']) - strtotime(date("y-m-d H:i:s"));
      if($time <= 0 )
        return "없음";
      else
        return floor($time / 3600)."시간 ".floor($time/60)."분 ".floor($time - (floor($time / 60) * 60))."초";
      unset($time);
    }
  }

  public function setMultiples(string $name, int $multiples) : void{
    if(strtotime($time) < 0)
    {
      trigger_error("Function setMultiples() 1 Argument value must be less than 0.", E_USER_ERROR);
    } else {
      $arr = $this->get($name);
      $arr['boster']['multiples'] = $multiples;
      $this->set($name, $arr);
      unset($arr);
    }
  }

  public function getMultiples(string $name) : int {
    if(!$this->isKey($name))
      return "Not Found";
    else
      return $this->get($name)['boster']['multuples'];
  }

 }

 class LevelSystem extends \pocketmine\plugin\PluginBase {
   use LevelMethod;

   private static $instance = null;

   public function getData() : Config {
     return new Config($this->getDataFolder() . "data.json", Config::JSON);

   }

   public function get(string $key) : ?array {
     if(!$this->getData()->exists($key))
      return null;

     return ((array)json_decode($this->getData()->get($key)));
   }

   public function set(string $key, $value = true) : void {
      $data = $this->getData();
      $data->set($key, json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_FORCE_OBJECT));
      $data->save();
      unset($data); // $data 변수 메모리 해제

   }

   public function iskey($key) : bool
   {
     if($this->getData()->exists($key))
     {
       return true;
     }else {
       return false;
     }

   }

   public static function getInstance() : LevelSystem //static 멤버 변수는 Heap 영역에 담겨 있음
   {
     return self::$instance;
   }


   public function onEnable() : void {
     self::$instance = $this;
     $this->getServer()->getPluginManager()->registerEvents(new class implements \pocketmine\event\Listener {

       public function JoinHandler(\pocketmine\event\player\PlayerJoinEvent $event) : void {
         $player = $event->getPlayer();
         $instance = LevelSystem::getInstance();

         if($instance->isKey($player->getName()))
          return;

         $instance->set($player->getName(), ['level' => 1, 'exp' => 0, 'maxExp' => 100, 'boster' => ['time' => "0", 'multiples' => 0]]);

         unset($player); //$player 메모리 할당 취소
         unset($instance);
       }
     }, $this);

     $path = $this->getDataFolder() . "data.json";

     if(!file_exists($path))
      file_put_contents($path, json_encode([], JSON_UNESCAPED_UNICODE));

     unset($path);
   }

 }
?>
