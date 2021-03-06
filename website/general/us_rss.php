<?php
	include $_SERVER['DOCUMENT_ROOT'].'/shared/shared.php';

	$news_list = News::load_from_db( 'DESC', 15, 0 );

	$time = $news_list[0]['published_at'];
	$last_mod = filemtime( $_SERVER['SCRIPT_FILENAME'] );
	if( $time < $last_mod )
	{
		$time = $last_mod;
	}

	$etag = md5( $time );

	header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $time) . ' GMT');
	header('ETag: ' . $etag );

	$if_last_mod = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) : 0;
	$if_not_match = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? $_SERVER['HTTP_IF_NONE_MATCH'] : 0;

	if ( $if_last_mod >= $time && $if_not_match == $etag )
	{
		header('HTTP/1.0 304 Not Modified');
		exit;
	}

	Page::setXML( 'application/rss+xml' );
	Page::Init();
?>
<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">
	<channel>
		<title>News of Illarion</title>
		<link><?php echo Page::getURL(); ?>/general/us_rss.php</link>
		<description>The current news of the online-roleplaygame Illarion</description>
		<language>en-us</language>
		<copyright>Illarion e.V.</copyright>
		<lastBuildDate><?php echo date(DATE_RSS, $time ); ?></lastBuildDate>
		<pubDate><?php echo date(DATE_RSS, $time ); ?></pubDate>
		<managingEditor>webmaster@illarion.org</managingEditor>
		<webMaster>webmaster@illarion.org</webMaster>
		<?php News::show( $news_list, 'rss' ); ?>
	</channel>
</rss>