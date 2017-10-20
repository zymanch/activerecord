<?php
/**
 * This is the template for generating the ActiveQuery class.
 */

/* @var $this ActiveRecord\web\View */
/* @var $generator ActiveRecord\gii\generators\model\Generator */
/* @var $tableName string full table name */
/* @var $className string class name */
/* @var $tableSchema ActiveRecord\db\TableSchema */
/* @var $labels string[] list of attribute labels (name => label) */
/* @var $rules string[] list of validation rules */
/* @var $relations array list of relations (name => relation declaration) */
/* @var $className string class name */
/* @var $modelClassName string related model class name */

echo "<?php\n";
?>

namespace <?= $ns.'\\'.$sub ?>;
use ActiveRecord\Criteria;
use <?= $ns.'\\'.$mainQueryClassName;?>;

/**
 * This is the ActiveQuery class for [[<?= $ns.'\\'.$mainClassName ?>]].
<?php foreach ($tableSchema->columns as $column): ?>
 * @method <?= $mainQueryClassName ?> filterBy<?= str_replace('_', '', ucwords($column->name, '_')); ?>($value, $criteria = null)
<?php endforeach; ?>
<?php foreach ($tableSchema->columns as $column): ?>
  * @method <?= $mainQueryClassName ?> orderBy<?= str_replace('_', '', ucwords($column->name, '_')); ?>($order = Criteria::ASC)
<?php endforeach; ?>
<?php foreach ($relations as $name => $relation): ?>
<?php $parts = explode('\\',$name);?>
  * @method <?= $mainQueryClassName ?> with<?= $parts[sizeof($parts)-1] ?>($params = [])
  * @method <?= $mainQueryClassName ?> joinWith<?= $parts[sizeof($parts)-1] ?>($params = null, $joinType = 'LEFT JOIN')
<?php endforeach; ?>
 */
class <?= $queryClassName ?> extends <?= '\\' . ltrim($generator->queryBaseClass, '\\') . "\n" ?>
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \<?= $ns.'\\'.$mainClassName ?>[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \<?= $ns.'\\'.$mainClassName ?>|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @return \<?= $ns.'\\'.$mainQueryClassName ?>
     */
    public static function model()
    {
        return new \<?=$ns.'\\'.$mainQueryClassName;?>(\<?= $ns.'\\'.$mainClassName;?>::class);
    }
}
