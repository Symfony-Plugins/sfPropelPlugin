<?php $form = $this->getFormObject() ?>
<?php if (isset($this->params['route_prefix']) && $this->params['route_prefix']): ?>
[?php echo form_tag_for($form, '@<?php echo $this->getPluralName() ?>') ?]
<?php else: ?>
<form action="[?php echo url_for('<?php echo $this->getModuleName() ?>/'.($form->getObject()->isNew() ? 'create' : 'update').(!$form->getObject()->isNew() ? '?<?php echo $this->getPrimaryKeyUrlParams('$form->getObject()', true) ?> : '')) ?]" method="POST" [?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?]>
[?php if (!$form->getObject()->isNew()): ?]
<input type="hidden" name="sf_method" value="PUT" />
[?php endif; ?]
<?php endif;?>
  <table>
    <tfoot>
      <tr>
        <td colspan="2">
<?php if (isset($this->params['route_prefix']) && $this->params['route_prefix']): ?>
          &nbsp;<a href="[?php echo url_for('<?php echo $this->getUrlForAction('list') ?>') ?]">Cancel</a>
<?php else: ?>
          &nbsp;<a href="[?php echo url_for('<?php echo $this->getModuleName() ?>/index') ?]">Cancel</a>
<?php endif; ?>
          [?php if (!$form->getObject()->isNew()): ?]
<?php if (isset($this->params['route_prefix']) && $this->params['route_prefix']): ?>
            &nbsp;[?php echo link_to('Delete', '<?php echo $this->getUrlForAction('delete') ?>', $form->getObject(), array('method' => 'delete', 'confirm' => 'Are you sure?')) ?]
<?php else: ?>
            &nbsp;[?php echo link_to('Delete', '<?php echo $this->getModuleName() ?>/delete?<?php echo $this->getPrimaryKeyUrlParams('$form->getObject()', true) ?>, array('post' => true, 'confirm' => 'Are you sure?')) ?]
<?php endif; ?>
          [?php endif; ?]
          <input type="submit" value="Save" />
        </td>
      </tr>
    </tfoot>
    <tbody>
<?php if (isset($this->params['non_verbose_templates']) && $this->params['non_verbose_templates']): ?>
      [?php echo $form ?]
<?php else: ?>
      [?php echo $form->renderGlobalErrors() ?]
<?php foreach ($form->getWidgetSchema()->getPositions() as $i => $name): ?>
<?php if ($form[$name]->isHidden()) continue ?>
      <tr>
        <th><?php echo $form[$name]->renderLabel() ?></th>
        <td>
          [?php echo $form['<?php echo $name ?>']->renderError() ?]
          [?php echo $form['<?php echo $name ?>'] ?]
<?php $i == $this->getLastNonHiddenField() and print $this->getHiddenFieldsAsString() ?>
        </td>
      </tr>
<?php endforeach; ?>
<?php endif; ?>
    </tbody>
  </table>
</form>
