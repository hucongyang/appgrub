<?php
/* @var $this IyourlController */
/* @var $model iyourl */

$this->breadcrumbs=array(
	'Iyourls'=>array('index'),
	$model->title=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List iyourl', 'url'=>array('index')),
	array('label'=>'Create iyourl', 'url'=>array('create')),
	array('label'=>'View iyourl', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage iyourl', 'url'=>array('admin')),
);
?>

<h1>Update iyourl <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>