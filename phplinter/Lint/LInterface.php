<?php
/**
----------------------------------------------------------------------+
*  @desc			Lint an interface.
----------------------------------------------------------------------+
*  @file 			Lint_interface.php
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
namespace phplinter\Lint;
class LInterface extends BaseLint implements ILint {
	/**
	----------------------------------------------------------------------+
	* @desc 	Analyze interface
	----------------------------------------------------------------------+
	*/
	public function _lint() {
		$regex = $this->rules['CON_INTERFACE_NAME']['compare'];
		if(!(substr($this->node->name, 0, 2) == '__') 
			&& !preg_match($regex, $this->node->name))
			$this->report('CON_INTERFACE_NAME', $regex);
			
		if($this->node->empty) 
			$this->report('WAR_EMPTY_INTERFACE');
		
		if(!$this->node->dochead)
			$this->report('DOC_NO_DOCHEAD_INTERFACE');
			
		$len = $this->node->length;
		if($len > $this->rules['REF_CLASS_LENGTH']['compare'])
			$this->report('REF_CLASS_LENGTH', $len);	
			
		$regex = $this->rules['CON_CLASS_NAME']['compare'];
		if(!preg_match($regex, $this->node->name))
			$this->report('CON_INTERFACE_NAME', $regex);
			
		$et = $this->node->tokens;
		$locals 	= array();
		for($i = 0;$i < $this->node->token_count;$i++) {
			switch($et[$i][0]) {
				default:
					$this->common_tokens($i);
					break;
			}
		}
		
		if(!empty($this->locals[T_METHOD]) && 
			in_array($this->node->name, $this->locals[T_METHOD]))
			$this->report('WAR_OLD_STYLE_CONSTRUCT');
		
		return $this->reports;
	}
}