<?php
require_once 'configs/config.php';
require_once 'tasks/CoolShow/Theme.class.php';
require_once 'tasks/CoolShow/Wallpaper.class.php';
require_once 'tasks/CoolShow/Ring.class.php';
require_once 'tasks/CoolShow/Font.class.php';
require_once 'tasks/CoolShow/Scene.class.php';
require_once 'tasks/CoolShow/LiveWallpaper.class.php';
require_once 'tasks/CoolShow/Alarm.class.php';
		
class CoolShowFactory
{
	public function __construct()
	{
	}
	
	static function getCoolShow($nCoolType)
	{
		$coolshow = null;
		switch ($nCoolType)
		{
			case COOLXIU_TYPE_THEMES:
			case COOLXIU_TYPE_THEMES_CONTACT:
			case COOLXIU_TYPE_THEMES_MMS:
			case COOLXIU_TYPE_THEMES_ICON:
				$coolshow = new Theme($nCoolType);
				break;
			case COOLXIU_TYPE_WALLPAPER:
			case COOLXIU_TYPE_ANDROIDESK_WALLPAPER:
			case COOLXIU_TYPE_SCENE_WALLPAPER:
				$coolshow = new Wallpaper();
				break;
			case COOLXIU_TYPE_RING:
				$coolshow = new Ring();
				break;
			case COOLXIU_TYPE_FONT:
				$coolshow = new Font();
				break;
			case COOLXIU_TYPE_SCENE:
				$coolshow = new Scene();
				break;
			case COOLXIU_TYPE_WIDGET:
				$coolshow = new Widget();
				break;
			case COOLXIU_TYPE_ALBUMS:
				$coolshow = new Albums();
				break;
			case COOLXIU_TYPE_LIVE_WALLPAPER:
				//动态壁纸资源为锁屏的一部分
				$coolshow = new Scene();
				$coolshow->setType('livewallpapers');
				break;
			case COOLXIU_TYPE_ALARM:
				$coolshow = new Alarm();
				break;
			default:
				$coolshow = new Theme();
				break;
		}
		return $coolshow;
	}

}