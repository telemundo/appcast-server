<?php
header('Content-type: application/rss+xml');
?><<?php echo '?'; ?>xml version="1.0" encoding="utf-8"<?php echo '?'; ?>>
<rss version="2.0" xmlns:sparkle="http://www.andymatuschak.org/xml-namespaces/sparkle" xmlns:dc="http://purl.org/dc/elements/1.1/">
  <channel>
    <title><?php echo ucfirst($application); ?> Changelog</title>
    <link><?php echo $canonical; ?></link>
    <description>Most recent changes with links to updates.</description>
    <language>en</language>
<?php echo $content; ?>
  </channel>
</rss>