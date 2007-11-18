<?php
/*
 *  $Id: ForeignKey.php 816 2007-11-18 23:29:44Z heltem $
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information please see
 * <http://propel.phpdb.org>.
 */

require_once 'propel/engine/database/model/XMLElement.php';

/**
 * A Class for information about foreign keys of a table.
 *
 * @author     Hans Lellelid <hans@xmpl.org>
 * @author     Fedor <fedor.karpelevitch@home.com>
 * @author     Daniel Rall <dlr@finemaltcoding.com>
 * @version    $Revision: 816 $
 * @package    propel.engine.database.model
 */
class ForeignKey extends XMLElement {

	private $foreignTableName;
	private $name;
	private $phpName;
	private $refPhpName;
	private $onUpdate;
	private $onDelete;
	private $parentTable;
	private $localColumns = array();
	private $foreignColumns = array();

	// the uppercase equivalent of the onDelete/onUpdate values in the dtd
	const NONE     = "";            // No "ON [ DELETE | UPDATE]" behaviour specified.
	const NOACTION  = "NO ACTION";
	const CASCADE  = "CASCADE";
	const RESTRICT = "RESTRICT";
	const SETDEFAULT  = "SET DEFAULT";
	const SETNULL  = "SET NULL";

	/**
	 * Sets up the ForeignKey object based on the attributes that were passed to loadFromXML().
	 * @see        parent::loadFromXML()
	 */
	protected function setupObject()
	{
		$this->foreignTableName = $this->getAttribute("foreignTable");
		$this->name = $this->getAttribute("name");
		$this->phpName = $this->getAttribute("phpName");
		$this->refPhpName = $this->getAttribute("refPhpName");
		$this->onUpdate = $this->normalizeFKey($this->getAttribute("onUpdate"));
		$this->onDelete = $this->normalizeFKey($this->getAttribute("onDelete"));
	}

	/**
	 * normalizes the input of onDelete, onUpdate attributes
	 */
	private function normalizeFKey($attrib)
	{
		if ($attrib === null  || strtoupper($attrib) == "NONE") {
			$attrib = self::NONE;
		}
		$attrib = strtoupper($attrib);
		if ($attrib == "SETNULL") {
			$attrib =  self::SETNULL;
		}
		return $attrib;
	}

	/**
	 * returns whether or not the onUpdate attribute is set
	 */
	public function hasOnUpdate()
	{
	   return ($this->onUpdate !== self::NONE);
	}

	/**
	 * returns whether or not the onDelete attribute is set
	 */
	public function hasOnDelete()
	{
	   return ($this->onDelete !== self::NONE);
	}

	/**
	 * returns the onUpdate attribute
	 */
	public function getOnUpdate()
	{
	   return $this->onUpdate;
	}

	/**
	 * returns the onDelete attribute
	 */
	public function getOnDelete()
	{
	   return $this->onDelete;
	}

	/**
	 * sets the onDelete attribute
	 */
	public function setOnDelete($value)
	{
	   $this->onDelete = $this->normalizeFKey($value);
	}

	/**
	 * sets the onUpdate attribute
	 */
	public function setOnUpdate($value)
	{
	   $this->onUpdate = $this->normalizeFKey($value);
	}

	/**
	 * Returns the name attribute.
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Sets the name attribute.
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * Gets the phpName for this foreign key (if any).
	 * @return     string
	 */
	public function getPhpName()
	{
		return $this->phpName;
	}

	/**
	 * Sets a phpName to use for this foreign key.
	 * @param      string $name
	 */
	public function setPhpName($name)
	{
		$this->phpName = $name;
	}

	/**
	 * Gets the refPhpName for this foreign key (if any).
	 * @return     string
	 */
	public function getRefPhpName()
	{
		return $this->refPhpName;
	}

	/**
	 * Sets a refPhpName to use for this foreign key.
	 * @param      string $name
	 */
	public function setRefPhpName($name)
	{
		$this->refPhpName = $name;
	}

	/**
	 * Get the foreignTableName of the FK
	 */
	public function getForeignTableName()
	{
		return $this->foreignTableName;
	}

	/**
	 * Set the foreignTableName of the FK
	 */
	public function setForeignTableName($tableName)
	{
		$this->foreignTableName = $tableName;
	}

	/**
	 * Gets the resolved foreign Table model object.
	 * @return     Table
	 */
	public function getForeignTable()
	{
		return $this->getTable()->getDatabase()->getTable($this->getForeignTableName());
	}

	/**
	 * Set the parent Table of the foreign key
	 */
	public function setTable(Table $parent)
	{
		$this->parentTable = $parent;
	}

	/**
	 * Get the parent Table of the foreign key
	 */
	public function getTable()
	{
		return $this->parentTable;
	}

	/**
	 * Returns the Name of the table the foreign key is in
	 */
	public function getTableName()
	{
		return $this->parentTable->getName();
	}

	/**
	 * adds a new reference entry to the foreign key
	 */
	public function addReference($p1, $p2 = null)
	{
		if (is_array($p1)) {
			$this->addReference(@$p1["local"], @$p1["foreign"]);
		} else {
			$this->localColumns[] = $p1;
			$this->foreignColumns[] = $p2;
		}
	}

	/**
	 * Return a comma delimited string of local column names
	 * @deprecated because Column::makeList() is deprecated; use the array-returning getLocalColumns() and DDLBuilder->getColumnList() instead instead.
	 */
	public function getLocalColumnNames()
	{
		return Column::makeList($this->getLocalColumns(), $this->getTable()->getDatabase()->getPlatform());
	}

	/**
	 * Return a comma delimited string of foreign column names
	 * @deprecated because Column::makeList() is deprecated; use the array-returning getForeignColumns() and DDLBuilder->getColumnList() instead instead.
	 */
	public function getForeignColumnNames()
	{
		return Column::makeList($this->getForeignColumns(), $this->getTable()->getDatabase()->getPlatform());
	}

	/**
	 * Return an array of local column objects.
	 * @return     array Column[]
	 */
	public function getLocalColumns()
	{
		return $this->localColumns;
	}

	/**
	 * Utility method to get local column to foreign column
	 * mapping for this foreign key.
	 */
	public function getLocalForeignMapping()
	{
		$h = array();
		for ($i=0, $size=count($this->localColumns); $i < $size; $i++) {
			$h[$this->localColumns[$i]] = $this->foreignColumns[$i];
		}
		return $h;
	}

	/**
	 * Get the foreign column mapped to specified local column.
	 * @return     string Column name.
	 */
	public function getMappedForeignColumn($local)
	{
		$m = $this->getLocalForeignMapping();
		if (isset($m[$local])) {
			return $m[$local];
		}
		return null;
	}

	/**
	 * Get the local column mapped to specified foreign column.
	 * @return     string Column name.
	 */
	public function getMappedLocalColumn($foreign)
	{
		$m = $this->getForeignLocalMapping();
		if (isset($m[$foreign])) {
			return $m[$foreign];
		}
		return null;
	}

	/**
	 * Return an array of foreign column objects.
	 * @return     array Column[]
	 */
	public function getForeignColumns()
	{
		return $this->foreignColumns;
	}

	/**
	 * Utility method to get local column to foreign column
	 * mapping for this foreign key.
	 */
	public function getForeignLocalMapping()
	{
		$h = array();
		for ($i=0, $size=count($this->localColumns); $i < $size; $i++) {
			$h[ $this->foreignColumns[$i] ] = $this->localColumns[$i];
		}
		return $h;
	}

	/**
	 * Whether this foreign key is also the primary key of the local table.
	 *
	 * @return     boolean
	 */
	public function isLocalPrimaryKey()
	{
		$localCols = $this->getLocalColumns();

		$localPKColumnObjs = $this->getTable()->getPrimaryKey();

		$localPKCols = array();
		foreach ($localPKColumnObjs as $lPKCol) {
			$localPKCols[] = $lPKCol->getName();
		}
//
//		print "Local key columns: \n";
//		print_r($localCols);
//
//		print "Local table primary key columns: \n";
//		print_r($localPKCols);

		return (!array_diff($localPKCols, $localCols));
	}

	/**
	 * String representation of the foreign key. This is an xml representation.
	 */
	public function toString()
	{
		$result = "    <foreign-key foreignTable=\""
			. $this->getForeignTableName()
			. "\" name=\""
			. $this->getName()
			. "\""
			. ($this->getPhpName() ? " phpName=\"".($this->getPhpName())."\"" : "")
			. ($this->getRefPhpName() ? " refPhpName=\"".($this->getRefPhpName())."\"" : "")
			.">\n";

		for ($i=0, $size=count($this->localColumns); $i < $size; $i++) {
			$result .= "        <reference local=\""
				. $this->localColumns[$i]
				. "\" foreign=\""
				. $this->foreignColumns[$i]
				. "\"/>\n";
		}
		$result .= "    </foreign-key>\n";
		return $result;
	}
}
