<?php /** @var $block array */

use Sprint\Editor\Module;

$file = snippet . phpModule::getSnippetsDir() . $block['file'];

if ($block['file'] && is_file($file)) {
    include $file;
}
