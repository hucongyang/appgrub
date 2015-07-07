<?php
/* @var $this IyourlController */
/* @var $model iyourl */

$this->breadcrumbs=array(
	'Iyourls'=>array('index'),
	$model->title,
);

$this->menu=array(
	array('label'=>'List iyourl', 'url'=>array('index')),
	array('label'=>'Create iyourl', 'url'=>array('create')),
	array('label'=>'Update iyourl', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete iyourl', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage iyourl', 'url'=>array('admin')),
);
?>

<h1>View iyourl #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'uid',
		'title',
		'url',
		'picurl',
		'domain',
		'category',
		'created',
		'score',
		'rank',
		'comments',
		'thirdid',
	),
)); ?>
