<?php
/* @var $this IyourlController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Iyourls',
);

$this->menu=array(
	array('label'=>'Create iyourl', 'url'=>array('create')),
	array('label'=>'Manage iyourl', 'url'=>array('admin')),
);
?>

<h1>Iyourls</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
