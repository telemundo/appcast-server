<?php foreach ($items as $version => $item) { ?>
      <item>
        <title>Subtitler <?php echo $version; ?></title>
        <sparkle:releaseNotesLink><?php echo $item['notes']; ?></sparkle:releaseNotesLink>
        <sparkle:minimumSystemVersion>10.6.8</sparkle:minimumSystemVersion>
        <pubDate><?php echo date('r', $item['date']); ?></pubDate>
        <enclosure url="<?php echo $item['archive']; ?>" sparkle:version="<?php echo $item['version']; ?>" length="<?php echo $item['size']; ?>" sparkle:dsaSignature="<?php echo $item['signature']; ?>" type="application/octet-stream" />
      </item>
<?php } ?>