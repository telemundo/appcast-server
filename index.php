<?php
ini_set('short_open_tag', 0);

require_once 'vendors/limonade/lib/limonade.php';

dispatch('/check/:application', 'check_index');
function check_index($application = NULL) {
    $url = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']).'/data/'.$application;
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
                                'version'   => (int)$metadata['version'],
                                'archive'   => $baseurl.'/'.$metadata['archive'],
                                'date'      => (int)$metadata['date'],
                                'size'      => (int)$metadata['size'],
                                'signature' => $metadata['signature'],
                                'notes'     => $baseurl.'/release.txt'
                            );
                            $items[$entry] = $item;
                        }
                    }
                }
            }

            set('application', $application);
            set('items', $items);
            set('canonical', $url);

            return html('check.xml.php', 'layout.xml.php');
        } else {
            halt(NOT_FOUND, "The application you have requested doesn't exists.");
        }
        arsort($items);
    } catch(Exception $e) {}
}

dispatch('/download/:application', 'download_index');
function download_index($application = NULL) {
    $url = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']).'/data/'.$application;
    try {
        $directory = dirname(__FILE__).'/data/'.$application;
        if ($handle = opendir($directory)) {
            while (FALSE !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    $basename = realpath($directory.'/'.$entry);
                    $fileinfo = new SplFileInfo($basename);
                    if (is_dir($basename)) {
                        if (preg_match('@^[0-9]+\.[0-9]+\.[0-9]+$@i', $entry)) {
                            $baseurl  = $url.'/'.$entry;
                            $metadata = json_decode(file_get_contents($basename.'/metadata.json'), TRUE);
                            $filename  = $metadata['archive'];
                            $archive = $basename.'/'.$filename;
                            break;
                        }
                    }
                }
            }

            header('Content-disposition: attachment; filename='.$filename);
            header("Content-Type: application/octect-stream");
            header("Cache-Control: no-cache, private");
            header("Edge-control: no-store");

            return render_file($archive);
        } else {
            halt(NOT_FOUND, "The application you have requested doesn't exists.");
        }
    } catch(Exception $e) {}
}

run();
