  public function executeIndex($request)
  {
<?php if (isset($this->params['with_propel_route']) && $this->params['with_propel_route']): ?>
    $this-><?php echo $this->getPluralName() ?> = $request->getAttribute('<?php echo $this->getPluralName() ?>');
<?php else: ?>
    $this-><?php echo $this->getPluralName() ?> = <?php echo $this->getPeerClassName() ?>::doSelect(new Criteria());
<?php endif; ?>
  }