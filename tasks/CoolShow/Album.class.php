<?php
require_once 'lib/WriteLog.lib.php';
require_once 'tasks/CoolShow/CoolShow.class.php';
require_once 'tasks/CoolShow/AlbumSql.sql.php';
require_once 'tasks/protocol/BannerProtocol.php';
require_once 'configs/config.php';

class Album extends CoolShow
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getSelectBannerListSql($nCoolType, $bAlbum = 0, $nStart = 0, $nNum = 0, $nProtocolCode = 0, $strProduct)
    {
        $strCondition = '';
        if ($nProtocolCode < 3) {
            $strCondition .= ' AND H5 = 0 ';
        }
        
        // 按机型过滤
        $tmparray1 = explode('8681', $strProduct);
        $tmparray2 = explode('8692', $strProduct);
        $tmparray3 = explode('8676', $strProduct);
        global $g_arr_product_filter;
        $qvalid = 0;
        if (count($tmparray1) == 1 && count($tmparray2) == 1 && count($tmparray3) == 1) {
            $qvalid = 1;
        }
        
        if ($qvalid) {
            $strCondition .= sprintf(' AND identity not in %s ', $g_arr_product_filter['banner']['theme']);
        }
        $strCondition .= ' AND album = 0  ORDER BY update_time DESC ';
        if ($bAlbum) {
            if ($qvalid) {
                $strCondition = sprintf(' AND identity not in %s ', $g_arr_product_filter['banner']['theme']);
                $strCondition .= sprintf(' ORDER BY update_time DESC LIMIT %d, %d ', $nStart, $nNum);
            } else {
                $strCondition = sprintf(' ORDER BY update_time DESC LIMIT %d, %d ', $nStart, $nNum);
            }
        }
        
        $sql = sprintf(SQL_SELECT_ALBUM_LIST, $nCoolType, $strCondition);
        return $sql;
    }

    public function getSelectAndroideskBannerListSql()
    {
        $sql = sprintf(SQL_SELECT_ANDROIDESK_ALBUM_LIST);
        return $sql;
    }

    public function getLucene($rows)
    {
        return $this->getProtocol($rows);
    }

    public function getProtocol($rows, $nType = 0, $bAlbum = 0)
    {
        $arrTop = array();
        $arrBottom = array();
        $arrFoot = array();
        $arrAll = array();
        foreach ($rows as $row) {
            $banner = new BannerProtocol();
            $banner->setProtocol($row, $nType);
            $banner->setType($nType);
            if ($this->_bSceneWallpaer) {
                $banner->setSubType(1);
            }
            $bIsTop = isset($row['istop']) ? $row['istop'] : 0;
            switch ($bIsTop) {
                case 1:
                    array_push($arrTop, $banner);
                    break;
                case 0:
                    array_push($arrBottom, $banner);
                    break;
                case 2:
                    array_push($arrFoot, $banner);
                    break;
            }
            array_push($arrAll, $banner);
        }
        return array(
            'top' => $arrTop,
            'bottom' => $arrBottom,
            'foot' => $arrFoot,
            'all' => $arrAll
        );
    }

    public function getBannerProtocol($rows, $nType = 0)
    {
        $arrProtocol = array();
        foreach ($rows as $row) {
            $banner = new BannerProtocol();
            $banner->setProtocol($row, $nType);
            $banner->setType($nType);
            if ($this->_bSceneWallpaer) {
                $banner->setSubType(1);
            }
            array_push($arrProtocol, $banner);
        }
        return $arrProtocol;
    }

    public function setPayRatio()
    {}

    public function getCoolShowListSql($nStart = 0, $nLimit = 0)
    {}

    public function getCoolShowCountSql()
    {}

    public function getSelectBannerSql()
    {}

    public function getSelectRscSql($id)
    {}

    public function getSelectAlbumsSql($strId, $nStart = 0, $nNum = 100)
    {}

    public function getSelectInfoByIdSql($id, $nChannel = 0)
    {}

    public function getCoolShowWebSql($nSortType, $nStart = 0, $nLimit = 10)
    {}

    public function getCoolShowWebCountSql()
    {}

    public function getWebProtocol($rows)
    {}
}
