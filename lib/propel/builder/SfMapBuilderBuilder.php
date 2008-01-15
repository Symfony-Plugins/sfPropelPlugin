<?php

require_once 'propel/engine/builder/om/php5/PHP5MapBuilderBuilder.php';

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @package    symfony
 * @subpackage propel
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id$
 */
class SfMapBuilderBuilder extends PHP5MapBuilderBuilder
{
  public function build()
  {
    $code = parent::build();
    if (!DataModelBuilder::getBuildProperty('builderAddComments'))
    {
      $code = sfToolkit::stripComments($code);
    }

    return $code;
  }

  protected function addIncludes(&$script)
  {
    if (!DataModelBuilder::getBuildProperty('builderAddIncludes'))
    {
      return;
    }

    parent::addIncludes($script);
  }

  protected function addDoBuild(&$script)
  {
    parent::addDoBuild($script);

    // fix http://propel.phpdb.org/trac/ticket/235: Column sizes not being inserted into [table]MapBuilder->DoBuild() by PHP5MapBuilderBuilder
    $sizes = array();
    foreach ($this->getTable()->getColumns() as $col)
    {
      $sizes[$col->getPhpName()] = !$col->getSize() ? 'null' : $col->getSize();
    }

    // fix for handling varchars
    $script = preg_replace("/\\\$tMap\->addColumn\('([^']+)', '([^']+)', '([^']+)', PropelTypes\:\:VARCHAR, (false|true)\)/e", '"\\\$tMap->addColumn(\'$1\', \'$2\', \'$3\', PropelTypes::VARCHAR, $4, {$sizes[\'$2\']})"', $script);

    // fix for handling decimals
    $script = preg_replace("/\\\$tMap\->addColumn\('([^']+)', '([^']+)', '([^']+)', PropelTypes\:\:DECIMAL, (false|true), ([0-9]+),([0-9]+)\)/e", '"\\\$tMap->addColumn(\'$1\', \'$2\', \'$3\', PropelTypes::DECIMAL, $4, $5)"', $script);
  }
}
