<?php
/* @var $this IyourlController */
/* @var $model iyourl */

$this->breadcrumbs=array(
	'Iyourls'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List iyourl', 'url'=>array('index')),
	array('label'=>'Manage iyourl', 'url'=>array('admin')),
);
?>

<h1>Create iyourl</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>