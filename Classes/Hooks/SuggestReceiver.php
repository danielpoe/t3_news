<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Georg Ringer <typo3@ringerge.org>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/


/**
 * Custom suggest receiver for tags
 *
 * @author	Georg Ringer <typo3@ringerge.org>
 * @package	TYPO3
 * @subpackage	tx_news
 */
class tx_News_Hooks_SuggestReceiver extends t3lib_TCEforms_Suggest_DefaultReceiver{

	/**
	 * Queries a table for records and completely processes them
	 *
	 * Returns a two-dimensional array of almost finished records; the only need to be put into a <li>-structure
	 *
	 * If you subclass this class, you will most likely only want to overwrite the functions called from here, but not
	 * this function itself
	 *
	 * @param array $params
	 * @param object $ref The parent object
	 * @return mixed array of rows or FALSE if nothing found
	 */
	public function queryTable(&$params, $recursionCounter = 0) {
		$uid = t3lib_div::_GP('uid');
//		$pageId = t3lib_div::_GP('pid');

		$records = parent::queryTable($params, $recursionCounter);

		if (count($records) === 0) {
			$text = htmlspecialchars($params['value']);
$js = '
var value=\'' . $text . '\';

Ext.Ajax.request({
	url : \'ajax.php\' ,
	params : { ajaxID : \'News::createTag\', item:value,newsid:\'' . $uid . '\' },
	success: function ( result, request ) {
		var arr = result.responseText.split(\'-\');
		setFormValueFromBrowseWin(arr[5], arr[2] +  \'_\' + arr[0], arr[1]);
		TBE_EDITOR.fieldChanged(arr[3], arr[6], arr[4], arr[5]);
	},
	failure: function ( result, request) {
		Ext.MessageBox.alert(\'Failed\', result.responseText);
	}
});
';

$js = trim(str_replace('"', '\'', $js));
$link = implode(' ', explode(chr(10), $js));

			$records['tx_news_domain_model_tag_' . strlen($text)] = array (
				'text' => '<div onclick="' . $link . '">
							<span class="suggest-label">' . $text . '</span><br />
							<span class="suggest-path">
								<a>' .
									sprintf($GLOBALS['LANG']->sL('LLL:EXT:news/Resources/Private/Language/locallang_be.xml:tag_suggest'), $text) .
								'</a>
							</span></div>',
				'table' => 'tx_news_domain_model_tag',
				'class' => 'suggest-noresults'
			);
		}

		return $records;

	}

}
?>