<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $this ActiveRecord\web\View */
/* @var $generator ActiveRecord\gii\generators\model\Generator */
/* @var $tableName string full table name */
/* @var $className string class name */
/* @var $queryClassName string query class name */
/* @var $tableSchema ActiveRecord\db\TableSchema */
/* @var $labels string[] list of attribute labels (name => label) */
/* @var $rules string[] list of validation rules */
/* @var $relations array list of relations (name => relation declaration) */

echo "<?php\n";
?>

namespace <?= $ns.'\\'.$sub ?>;

class <?= $peerClassName ?>  {

<?php foreach ($tableSchema->columns as $column): ?>
    const <?= strtoupper($column->name);?> = "<?=$column->name;?>";
<?php endforeach; ?>

}
