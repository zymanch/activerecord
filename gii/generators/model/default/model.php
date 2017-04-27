<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $this ActiveRecord\web\View */
/* @var $generator ActiveRecord\gii\generators\model\Generator */
/* @var $tableName string full table name */
/* @var $className string class name */
/* @var $peerName string class name */
/* @var $queryClassName string query class name */
/* @var $tableSchema ActiveRecord\db\TableSchema */
/* @var $labels string[] list of attribute labels (name => label) */
/* @var $rules string[] list of validation rules */
/* @var $relations array list of relations (name => relation declaration) */

echo "<?php\n";
?>

namespace <?= $generator->ns ?>;

/**
 * This is the model class for table "<?= $generator->generateTableName($tableName) ?>".
 *
<?php foreach ($tableSchema->columns as $column): ?>
 * @method <?= $className ?> filterBy<?= str_replace('_', '', ucwords($column->name, '_')); ?>($value, $criteria = null)
<?php endforeach; ?>
<?php foreach ($tableSchema->columns as $column): ?>
 * @property <?= "{$column->phpType} \${$column->name}\n" ?>
<?php endforeach; ?>
<?php if (!empty($relations)): ?>
 *
<?php foreach ($relations as $name => $relation): ?>
 * @property <?= $relation[1] . ($relation[2] ? '[]' : '') . ' $' . lcfirst($name) . "\n" ?>
<?php endforeach; ?>
<?php endif; ?>
 */
class <?= $className ?> extends <?= '\\' . ltrim($generator->baseClass, '\\') . "\n" ?>
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '<?= $generator->generateTableName($tableName) ?>';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [<?= "\n            " . implode(",\n            ", $rules) . ",\n        " ?>];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
<?php foreach ($labels as $name => $label): ?>
            <?=$peerName;?>::<?= strtoupper($name)." => " . $generator->generateString($label) . ",\n" ?>
<?php endforeach; ?>
        ];
    }
<?php foreach ($relations as $name => $relation): ?>
<?php $parts = explode('\\',$name);?>
    /**
     * @return \ActiveRecord\db\ActiveQuery
     */
    <?php if (sizeof($parts) > 1):?>
public function get<?= $parts[sizeof($parts)-1] ?>s() {
        <?= $relation[0] . "\n" ?>
    }
    <?php else:?>
public function get<?= $parts[sizeof($parts)-1] ?>() {
        <?= $relation[0] . "\n" ?>
    }
    <?php endif;?>
<?php endforeach; ?>
<?php if ($queryClassName): ?>
<?php
    $queryClassFullName = ($generator->ns === $generator->queryNs) ? $queryClassName : '\\' . $generator->queryNs . '\\' . $queryClassName;
    echo "\n";
?>
    /**
     * @inheritdoc
     * @return <?= $queryClassFullName ?> the active query used by this AR class.
     */
    public static function find()
    {
        return new <?= $queryClassFullName ?>(get_called_class());
    }
<?php endif; ?>
}
