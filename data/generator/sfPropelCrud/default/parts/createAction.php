  public function executeCreate($request)
  {
<?php if (isset($this->params['with_propel_route']) && $this->params['with_propel_route']): ?>
<?php else: ?>
    $this->forward404Unless($request->isMethod('post'));

<?php endif; ?>
    $this->form = new <?php echo $this->getFormClassName() ?>();

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }
