<?php
/**
 * Request Dump element.  Dumps out HTTP request log information
 *
 * CakePHP 2.0
 * PHP version 5
 *
 * Copyright 2012, nojimage (http://php-tips.com/)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @version   2.0
 * @author    nojimage <nojimage at gmail.com>
 * @copyright 2012 nojimage (http://php-tips.com/)
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 * @package   Rest.View.Element
 */
if (!class_exists('ConnectionManager') || Configure::read('debug') < 2) {
	return false;
}
$noLogs = !isset($logs);
if ($noLogs):
	$sources = ConnectionManager::sourceList();

	$logs = array();
	foreach ($sources as $source):
		$db = ConnectionManager::getDataSource($source);
		if (!method_exists($db, 'getLog')):
			continue;
		endif;
		$logs[$source] = $db->getLog();
	endforeach;
endif;

if ($noLogs || isset($_forced_from_dbo_)):
	foreach ($logs as $source => $logInfo):
		$text = $logInfo['count'] > 1 ? 'requests' : 'request';
		$tableId = 'cakeRequestLog_' . preg_replace('/[^A-Za-z0-9_]/', '_', uniqid(time(), true));
		$caption = sprintf('<caption>(%s) %s %s took %s ms</caption>', $source, $logInfo['count'], $text, $logInfo['time']);
		?>
		<table class="cake-sql-log" id="<?php echo $tableId ?>" summary="Cake Request Log" cellspacing="0">
			<?php echo $caption; ?>
			<thead>
				<tr><th>Nr</th><th>Request</th><th>Response</th><th>Took (ms)</th></tr>
			</thead>
			<tbody>
				<?php foreach ($logInfo['log'] as $k => $i) : ?>
					<tr>
						<td rowspan="3"><?php echo $k + 1 ?></td>
						<td><?php echo $i['request_method']; ?></td>
						<td><?php echo $i['response_code']; ?></td>
						<td rowspan="3" style="text-align: right"><?php echo number_format($i['took']); ?></td>
					</tr>
					<tr>
						<td><?php echo $i['request_uri']; ?></td>
						<td><?php echo $i['response_type']; ?></td>
					</tr>
					<tr>
						<td><?php echo $i['request_body']; ?></td>
						<td><?php echo $i['response_body']; ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody></table>
		<?php
	endforeach;
else:
	echo '<p>Encountered unexpected $logs cannot generate Reuest log</p>';
endif;
