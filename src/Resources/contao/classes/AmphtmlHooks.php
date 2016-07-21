<?php

/*
 * Copyright pdir / digital agentur <develop@pdir.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pdir;

/**
 * Amphtml for Contao Open Source CMS
 *
 * Copyright (C) 2016 pdir / digital agentur
 *
 * @package    amphtml
 * @link       https://github.com/pdir/amphtml-bundle
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 * @author Philipp Seibt <seibt@pdir.de>
 *
 * Configures the Amphtml hooks.
 *
 */

class AmphtmlHooks extends \Controller
{
    /*
     * if amp is set, load the given layout from page root
     */
    public function ampGetPageLayout($objPage, &$objLayout, $objPageRegular)
    {
        $amphtml = (int) \PageModel::findByPk($objPage->rootId)->amphtml;
        if($amphtml == 1) {
            if ($amphtml && ($page = \PageModel::findByPk($objPage->id)) !== null) {
                $strUrl = \Controller::generateFrontendUrl($page->row());
                echo "<br><br><pre>"; print_r($page->row()); echo "</pre>";
                $objLayout->head .= '<link rel="amphtml" href="' . $strUrl . '?amp" />';
            }

            if(isset($_GET['amp'])) {
                $ampLayout = (int) \PageModel::findByPk($objPage->rootId)->ampLayout;
                $ampUseInLayout = \PageModel::findByPk($objPage->rootId)->ampUseInLayout;
                $objLayout = \LayoutModel::findById($ampLayout);

                // enable or disable columns in layout
                $desMod = deserialize($objLayout->modules);
                for($i = 0; $i <= count($desMod); $i++) {
                    if( stristr($ampUseInLayout,"head") && $desMod[$i]['col'] == "header" ) {
                        $desMod[$i]['enable'] = '1';
                    } else if( !stristr($ampUseInLayout,"head") && $desMod[$i]['col'] == "header" ) {
                        $desMod[$i]['enable'] = '0';
                    }
                    if( stristr($ampUseInLayout,"footer") && $desMod[$i]['col'] == "footer" ) {
                        $desMod[$i]['enable'] = '1';
                    } else if( !stristr($ampUseInLayout,"footer") && $desMod[$i]['col'] == "footer" ) {
                        $desMod[$i]['enable'] = '0';
                    }
                    if( stristr($ampUseInLayout,"left") && $desMod[$i]['col'] == "left" ) {
                        $desMod[$i]['enable'] = '1';
                    } else if( !stristr($ampUseInLayout,"left") && $desMod[$i]['col'] == "left" ) {
                        $desMod[$i]['enable'] = '0';
                    }
                    if( stristr($ampUseInLayout,"right") && $desMod[$i]['col'] == "right" ) {
                        $desMod[$i]['enable'] = '1';
                    } else if( !stristr($ampUseInLayout,"right") && $desMod[$i]['col'] == "right" ) {
                        $desMod[$i]['enable'] = '0';
                    }
                } $objLayout->modules = serialize($desMod);

                // load inline css from file or use user custom
                if(file_exists("../files/amphtml/amphtml_custom.css")) {
                    $amphtmlCss = file_get_contents("http://".$_SERVER['HTTP_HOST']."/files/amphtml/amphtml_custom.css");
                } else {
                    $amphtmlCss = file_get_contents("http://".$_SERVER['HTTP_HOST']."/files/amphtml/amphtml.css");
                }
                $objLayout->head = "<style amp-custom>".$amphtmlCss."</style>";
                $objLayout->head .= '<link rel="canonical" href="'.$strUrl.'" />';
            }
        }
    }
    /*
     * if amp is set, add amp param to all urls
     */
    public function ampGenerateFrontendUrl($arrRow, $strParams, $strUrl)
    {
        if(isset($_GET['amp'])) {
            return $strUrl = $strUrl . '?amp';
        }
        return $strUrl;
    }
}