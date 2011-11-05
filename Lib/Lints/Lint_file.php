<?php
/**
----------------------------------------------------------------------+
*  @desc			Lint a file.
----------------------------------------------------------------------+
*  @file 			Lint_file.php
*  @author 			Jóhann T. Maríusson <jtm@robot.is>
*  @since 		    Oct 29, 2011
*  @package 		PHPLinter
*  @copyright     
*    phplinter is free software: you can redistribute it and/or modify
*    it under the terms of the GNU General Public License as published by
*    the Free Software Foundation, either version 3 of the License, or
*    (at your option) any later version.
*
*    This program is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*    GNU General Public License for more details.
*
*    You should have received a copy of the GNU General Public License
*    along with this program.  If not, see <http://www.gnu.org/licenses/>.
----------------------------------------------------------------------+
*/
namespace PHPLinter;
class Lint_file extends BaseLint implements ILint {
	/**
	----------------------------------------------------------------------+
	* @desc 	Analyze file
	----------------------------------------------------------------------+
	*/
	public function _lint() {
		$fp = explode('/', $this->element->file);
		$pretty_file_name = $fp[count($fp)-1];
		$this->element->parent = $pretty_file_name;
		
		$lnum = 1;
		foreach(file($this->element->file) as $_) {
			$len = mb_strlen($_);
			if($len > $this->rules['CON_LINE_LENGTH']['compare']) {
				$this->report('CON_LINE_LENGTH', $len, $lnum);
			}
			$lnum++;
		}
		
		$tcnt = $this->element->token_count;
		$et = $this->element->tokens;
		for($i = 0;$i < $tcnt;$i++) {
			switch($et[$i][0]) {
				case T_CLOSE_TAG:
					if($this->find($i, T_OPEN_TAG, null) === false) {
						if(count($et) - $i > 1)
							if($this->next($i))
								$this->report('REF_HTML_AFTER_CLOSE', null, $et[$i][2]);
							else
								$this->report('WAR_WS_AFTER_CLOSE', null, $et[$i][2]);
					} else {
						$this->common_tokens($i);
					}
					break;
				default:
					$this->common_tokens($i);
					break;
			}
		}
		if($this->branches > $this->rules['REF_BRANCHES']['compare'])
			$this->report('REF_BRANCHES', $this->branches);
			
		return $this->reports;
	}
}