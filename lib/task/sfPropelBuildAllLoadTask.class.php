<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/sfPropelBaseTask.class.php');

/**
 * Generates Propel model, SQL, initializes database, and load data.
 *
 * @package    symfony
 * @subpackage propel
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id$
 */
class sfPropelBuildAllLoadTask extends sfPropelBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', null),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'propel'),
      new sfCommandOption('no-confirmation', null, sfCommandOption::PARAMETER_NONE, 'Do not ask for confirmation'),
      new sfCommandOption('skip-forms', 'F', sfCommandOption::PARAMETER_NONE, 'Skip generating forms')
    ));

    $this->aliases = array('propel-build-all-load');
    $this->namespace = 'propel';
    $this->name = 'build-all-load';
    $this->briefDescription = 'Generates Propel model, SQL, initializes database, and load data';

    $this->detailedDescription = <<<EOF
The [propel:build-all-load|INFO] task is a shortcut for four other tasks:

  [./symfony propel:build-all-load|INFO]

The task is equivalent to:

  [./symfony propel:build-all|INFO]
  [./symfony propel:data-load|INFO]

The task takes an application argument because of the [propel:data-load|COMMENT]
task. See [propel:data-load|COMMENT] help page for more information.

To bypass the confirmation, you can pass the [no-confirmation|COMMENT]
option:

  [./symfony propel:buil-all-load --no-confirmation|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    // load Propel configuration before Phing
    $databaseManager = new sfDatabaseManager($this->configuration);

    require_once dirname(__FILE__) . '/../propel/addon/sfPropelAutoload.php';

    $buildAll = new sfPropelBuildAllTask($this->dispatcher, $this->formatter);
    $buildAll->setCommandApplication($this->commandApplication);

    $buildAllOptions = array();
    if ($options['skip-forms'])
    {
      $buildAllOptions[] = '--skip-forms';
    }
    if ($options['no-confirmation'])
    {
      $buildAllOptions[] = '--no-confirmation';
    }
    $ret = $buildAll->run(array(), $buildAllOptions);

    if (0 == $ret)
    {
      $loadData = new sfPropelLoadDataTask($this->dispatcher, $this->formatter);
      $loadData->setCommandApplication($this->commandApplication);

      $options = array('--env='.$options['env'], '--connection='.$options['connection']);
      if (isset($this->options['application']))
      {
        $options[] = '--application='.$options['application'];
      }

      $loadData->run(array(), $options);
    }

    $this->cleanup();

    return $ret;
  }
}
