<?php

namespace Util;

class Helper {

    static public function execTime ($precision, $untilTimestamp=null) {
        $untilTimestamp = $untilTimestamp ? $untilTimestamp : time();
        $wastage = microtime(true) - START_TIME;
        return round($wastage*1000, 2);
    }

    static public function getUserLanguageCode() {
        $userLanguage = \GlobalEnv::get('app')->getCookie('lang.code');
        if (!$userLanguage)
            return \GlobalEnv::get('app')->config('translation.default.code');
        return $userLanguage;
    }

}
