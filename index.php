<?php
ini_set('short_open_tag', 0);

require_once 'vendors/limonade/lib/limonade.php';

dispatch('/check/:application', 'check_index');
function check_index($application = NULL) {
    $url = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']).'/data/'.$application;
    $items = array();
    try {
        $directory = dirname(__FILE__).'/data/'.$application;
        if ($handle = opendir($directory)) {
            while (FALSE !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    $fileinfo = new SplFileInfo($directory.'/'.$entry);
                    if ($fileinfo->isDir()) {
                        $basename = $fileinfo->getBasename();
                        if (preg_match('@^[0-9]+\.[0-9]+\.[0-9]+$@i', $basename)) {
                            $baseurl = $url.'/'.$basename;
                            $basedir = $fileinfo->getRealPath();
                            $metadata = json_decode(file_get_contents($basedir.'/metadata.json'), TRUE);
                            $item = array(
                                'version' => $basename,
                                'archive' => $baseurl.'/'.$metadata['archive'],
                                'notes'   => $baseurl.'/'.$metadata['notes'],
                                'date'    => (int)$metadata['date'],
                                'size'    => filesize($basedir.'/'.$metadata['archive']),
                                'signature' => $metadata['signature']
                            );
                            $items[$basename] = $item;
                        }
                    }
                }
            }
        }
        arsort($items);
    } catch(Exception $e) {}

    set('application', $application);
    set('items', $items);
    set('canonical', $url);
    return html('check.xml.php', 'layout.xml.php');
}

run();