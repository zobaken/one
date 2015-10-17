/**
 * Helper class to work with table "<?=$tableName?>".
 * Generated automatically. All changes will be lost.
*/

namespace <?=$namespace?>\Table;

class <?=$tableClassName?> extends \Core\AbstractTable {

    static $fields;
    static $table= '<?=$tableName?>';
    static $pk = [<?=implode(', ', $pk)?>];
    static $generated = [];
<?foreach($tableInfo as $field):?>

    /**
    * Field: <?=$tableName?>.<?=$field['Field']."\n"?>
    * @var <?=$field['Type']."\n"?>
    */
<?php if(preg_match('/^int/', $field['Type']) && $field['Default'] !== null): ?>
    public $<?=$field['Field']?> = <?=$field['Default']?>;
<?php elseif(preg_match('/^(char|varchar)/', $field['Type']) && $field['Default'] !== null): ?>
    public $<?=$field['Field']?> = '<?=addcslashes($field['Default'], "'")?>';
<?php else: ?>
    public $<?=$field['Field']?>;
<?php endif; ?>
<?php endforeach;?>

    /**
    * Get object by id
    * @param $id mixed Id
    * @return \<?=$namespace?>\<?=$className?>
    */
    static function get($id) {
        return forward_static_call_array(['\Core\AbstractTable', 'get'], func_get_args());
    }

    /**
    * Find object
    * @param string $where Where statement
    * @return \<?=$namespace?>\<?=$className?>
    */
    static function findRow($where) {
        return forward_static_call_array(['\Core\AbstractTable', 'findRow'], func_get_args());
    }

}
<?php if($generated): ?>

<?=$tableClassName?>::$generated = [
<?php foreach($generated as $field=>$generator): ?>
    '<?=$field?>' => '<?=$generator?>',
<?php endforeach;?>
];
<?php endif; ?>
