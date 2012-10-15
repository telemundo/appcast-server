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
                    $basename = realpath($directory.'/'.$entry);
                    $fileinfo = new SplFileInfo($basename);
                    if (is_dir($basename)) {
                        if (preg_match('@^[0-9]+\.[0-9]+\.[0-9]+$@i', $entry)) {
                            $baseurl = $url.'/'.$entry;
                            $metadata = json_decode(file_get_contents($basename.'/metadata.json'), TRUE);
                            $item = array(
                                'version'   => $metadata['version'],
                                'archive'   => $baseurl.'/'.$metadata['archive'],
                                'date'      => (int)$metadata['date'],
                                'size'      => (int)$metadata['size'],
                                'signature' => $metadata['signature'],
                                'notes'     => $baseurl.'/release.txt'
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