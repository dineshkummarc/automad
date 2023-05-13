<?php
/*
 *                    ....
 *                  .:   '':.
 *                  ::::     ':..
 *                  ::.         ''..
 *       .:'.. ..':.:::'    . :.   '':.
 *      :.   ''     ''     '. ::::.. ..:
 *      ::::.        ..':.. .''':::::  .
 *      :::::::..    '..::::  :. ::::  :
 *      ::'':::::::.    ':::.'':.::::  :
 *      :..   ''::::::....':     ''::  :
 *      :::::.    ':::::   :     .. '' .
 *   .''::::::::... ':::.''   ..''  :.''''.
 *   :..:::'':::::  :::::...:''        :..:
 *   ::::::. '::::  ::::::::  ..::        .
 *   ::::::::.::::  ::::::::  :'':.::   .''
 *   ::: '::::::::.' '':::::  :.' '':  :
 *   :::   :::::::::..' ::::  ::...'   .
 *   :::  .::::::::::   ::::  ::::  .:'
 *    '::'  '':::::::   ::::  : ::  :
 *              '::::   ::::  :''  .:
 *               ::::   ::::    ..''
 *               :::: ..:::: .:''
 *                 ''''  '''''
 *
 *
 * AUTOMAD
 *
 * Copyright (c) 2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Models\History;

defined('AUTOMAD') or die('Direct access not permitted!');

use Automad\Core\Automad;
use Automad\Core\Cache;
use Automad\Core\DataFile;
use Automad\Core\FileSystem;
use Automad\Core\Messenger;
use Automad\Core\Text;
use Automad\System\Fields;

/**
 * The page history class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2023 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class History {
	const FILENAME = '.history';

	/**
	 * The daily backup array.
	 */
	private array $daily = array();

	/**
	 * The current path the file was opened from.
	 */
	private string $historyPath = '';

	/**
	 * The hourly backup array.
	 */
	private array $hourly = array();

	/**
	 * The monthly backup array.
	 */
	private array $monthly = array();

	/**
	 * The current path the file was opened from.
	 */
	private string $pagePath = '';

	/**
	 * The array of revisions.
	 */
	private array $revisions = array();

	/**
	 * The constructor
	 */
	public function __construct() {
	}

	/**
	 * Define properties to be serialized.
	 *
	 * @return array
	 */
	public function __serialize(): array {
		return array(
			'hourly' => $this->hourly,
			'daily' => $this->daily,
			'monthly' => $this->monthly,
			'revisions' => $this->revisions
		);
	}

	/**
	 * Unserialize data.
	 *
	 * @param array $props
	 */
	public function __unserialize(array $props): void {
		$this->hourly = $props['hourly'] ?? array();
		$this->daily = $props['daily'] ?? array();
		$this->monthly = $props['monthly'] ?? array();
		$this->revisions = $props['revisions'] ?? array();
	}

	/**
	 * Commit a new version of page data to the history.
	 *
	 * @param array $data
	 */
	public function commit(array $data): void {
		$Revision = new Revision($data);

		$this->revisions[$Revision->hash] = $Revision;

		$this->hourly = $this->sliceRevisions($this->hourly);
		$this->hourly[date('Y-m-d-H')] = $Revision->hash;
		$this->daily = $this->sliceRevisions($this->daily);
		$this->daily[date('Y-m-d')] = $Revision->hash;
		$this->monthly = $this->sliceRevisions($this->monthly);
		$this->monthly[date('Y-m')] = $Revision->hash;

		$this->garbageCollectRevisions();

		FileSystem::write($this->historyPath, $this->serialize());
	}

	/**
	 * Get the version history for a given page path.
	 *
	 * @param string $pagePath
	 * @return History
	 */
	public static function get(string $pagePath): History {
		$historyPath = self::getFullPath($pagePath);
		$History = new History();

		if (is_readable($historyPath)) {
			$unserialized = self::unserialize(file_get_contents($historyPath));

			if ($unserialized instanceof History) {
				$History = $unserialized;
			}
		}

		$History->historyPath = $historyPath;
		$History->pagePath = $pagePath;

		return $History;
	}

	/**
	 * Get the commit log.
	 *
	 * @return array
	 */
	public function log(): array {
		$log = array();

		foreach (array_values($this->revisions) as $Revision) {
			$log[] = (object) array('hash' => $Revision->hash, 'time' => $Revision->time);
		}

		usort($log, function ($a, $b) {
			if ($a->time === $b->time) {
				return 0;
			}

			return $a->time < $b->time ? 1 : -1;
		});

		return $log;
	}

	/**
	 * Restore a given revision.
	 *
	 * @param string $hash
	 * @param string $title
	 * @param Messenger $Messenger
	 */
	public function restore(string $hash, string $title, Messenger $Messenger): void {
		if (!array_key_exists($hash, $this->revisions)) {
			$Messenger->setError(Text::get('pageRevisionNotFound'));

			return;
		}

		$revision = $this->revisions[$hash];

		// Note that the title has to stay unchanged in order to avoid unitentionally decoupling title and slug.
		$data = array_merge($revision->data, array(Fields::TITLE => $title));

		DataFile::write($data, $this->pagePath);
		$this->commit($revision->data);

		Cache::clear();
	}

	/**
	 * Filter our revisions that are no longer linked to a set such as hourly, daily or monthly.
	 */
	private function garbageCollectRevisions(): void {
		$hashes = array_values(array_merge($this->hourly, $this->daily, $this->monthly));
		$filtered = array();

		foreach ($this->revisions as $Revision) {
			if (in_array($Revision->hash, $hashes)) {
				$filtered[$Revision->hash] = $Revision;
			}
		}

		$this->revisions = $filtered;
	}

	/**
	 * Resolve the full filesystem path for a page history.
	 *
	 * @param string $pagePath
	 * @return string
	 */
	private static function getFullPath(string $pagePath): string {
		return AM_BASE_DIR . AM_DIR_PAGES . $pagePath . '/' . History::FILENAME;
	}

	/**
	 * Serialize and compress if possible.
	 *
	 * @return string
	 */
	private function serialize(): string {
		if (function_exists('gzcompress') && function_exists('gzuncompress')) {
			return gzcompress(serialize($this));
		}

		return serialize($this);
	}

	/**
	 * Slice a set of version to the latest 5.
	 *
	 * @param array $set
	 * @return array
	 */
	private function sliceRevisions(array $set): array {
		krsort($set);

		return array_slice($set, 0, 5);
	}

	/**
	 * Unserialize and uncompress if possible.
	 *
	 * @param string $serialized
	 * @return bool|History
	 */
	private static function unserialize(string $serialized): History | bool {
		if (function_exists('gzcompress') && function_exists('gzuncompress')) {
			return unserialize(gzuncompress($serialized));
		}

		return unserialize($serialized);
	}
}