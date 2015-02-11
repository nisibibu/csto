/**
 * 複合主キーのテーブルを更新するためのモデル
 *
 * 継承のサンプル
 * <code>
 * class Example extends AbstractCompositeKeyModel {
 *     protected $tableName = 'example';
 *     protected $fieldsDefinition = array(
 *             'pkey_1'   => 'integer',
 *             'pkey_2'   => 'integer',
 *             'pkey_3'   => 'string',
 *             'column_1' => 'string',
 *             'column_2' => 'text',
 *             'column_3' => 'integer',
 *         );
 *     protected $compositePrimaryKey =  array('pkey_1', 'pkey_2', 'pkey_3');
 * }
 * </code>
 *
 * @author IKEDA Youhei <youhey.ikeda@gmail.com>
 */
abstract class AbstractCompositeKeyModel extends AppModel {

    /**
     * O/Rマッパー
     * 
     * <p>複合主キーに非対応なCakePHPのO/Rマッピングツールを使用しない。</p>
     * 
     * @var mixed
     */
    public $useTable = false;

    /**
     * テーブル名
     * 
     * @var string
     */
    protected $tableName = null;

    /**
     * テーブルのフィールド
     * 
     * @var array
     */
    protected $fieldsDefinition = array();

    /**
     * 複合主キー
     * 
     * @var array
     */
    protected $compositePrimaryKey =  array();

    /**
     * Constructor.
     *
     * @param mixed $id Set this ID for this model on startup, can also be an array of options, see above.
     * @param string $table Name of database table to use.
     * @param string $ds DataSource connection name.
     */
    public function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);

        if (empty($this->tableName)) {
            trigger_error("CardRec::{$this->name} was useful table name does not exist.", E_USER_ERROR);
        }
        if (empty($this->fieldsDefinition)) {
            trigger_error("CardRec::{$this->name} was fields definition does not exist.", E_USER_ERROR);
        }
        if (empty($this->compositePrimaryKey)) {
            trigger_error("CardRec::{$this->name} was composite primary key does not exist.", E_USER_ERROR);
        }
    }

    /**
     * 複合主キーに対応したモデルのCREATE
     * 
     * <p>複合キーに未対応なので、O/Rマッピングツールは使用しない。<br />
     * <p>レコード追加に必要なSQLを自前で組み立てて実行する。</p>
     * 
     * @return mixed 実行結果
     */
    public function manuallyCreate() {
        $dataSource = $this->getDataSource();

        $option   = array();
        $prepared = $this->beforeSave($option);
        if (!$prepared) {
            $result = false;
        } else {
            list($bufFields, $bufValues) = $this->buildQueryToUpdateAllItems();

            $fullTableName = $dataSource->fullTableName($this->tableName);
            $insertFields  = implode(', ', $bufFields);
            $insertValues  = implode(', ', $bufValues);

            $query = array('table' => $fullTableName, 
                           'fields' => $insertFields, 
                           'values' => $insertValues);

            $statement = $dataSource->renderStatement('create', $query);

            $dataSource->execute($statement);
            $result = $dataSource->hasResult();
        }
        $this->afterSave(true);

        return $result;
    }

    /**
     * 複合主キーに対応したモデルのUPDATE
     * 
     * <p>複合キーに未対応なので、O/Rマッピングツールは使用しない。<br />
     * <p>更新に必要なSQLを自前で組み立てて実行する。</p>
     * 
     * @return mixed 実行結果
     */
    public function manuallyUpdate() {
        $dataSource = $this->getDataSource();

        $result = false;
        if ($this->manuallyExists()) {
            $option   = array();
            $prepared = $this->beforeSave($option);
            if ($prepared) {
                list($bufFields, $bufValues) = $this->buildQueryToUpdateAllItems();

                $fullTableName = $dataSource->fullTableName($this->tableName);
                $rawConditions = $this->builConditionsOfPrimaryKey();
                $conditions    = $dataSource->conditions($rawConditions, true, true, $this);

                $combined = array_combine($bufFields, $bufValues);
                $updates  = array(); 
                foreach ($combined as $field => $value) {
                    if ($value === null) {
                        $updates[] = "{$field} = NULL";
                    } else {
                        $updates[] = "{$field} = {$value}";
                    }
                }
                $updateFields = implode(', ', $updates);

                $query    = array('table'      => $fullTableName,
                                  'fields'     => $updateFields,
                                  'conditions' => $conditions);

                $statement  = $dataSource->renderStatement('update', $query);
                $dataSource->execute($statement);
                $result = $dataSource->hasResult();
            }
            $this->afterSave(false);
        }

        return $result;
    }

    /**
     * 複合主キーに対応したモデルのEXISTS
     * 
     * <p>複合キーに未対応なので、O/Rマッピングツールは使用しない。<br />
     * <p>問い合わせに必要なSQLを自前で組み立てて実行する。</p>
     * 
     * @return mixed 実行結果
     */
    public function manuallyExists() {
        $dataSource = $this->getDataSource();

        $fullTableName = $dataSource->fullTableName($this->tableName);
        $rawConditions = $this->builConditionsOfPrimaryKey();
        $conditions    = $dataSource->conditions($rawConditions, true, true, $this);
        $query         = array('fields'     => 'COUNT(*) AS numrows', 
                               'table'      => $fullTableName,
                               'alias'      => '',
                               'joins'      => '',
                               'conditions' => $conditions, 
                               'group'      => '',
                               'order'      => '',
                               'limit'      => '');
        $statement     = $dataSource->renderStatement('select', $query);
        $result        = $dataSource->fetchRow($statement);

        $numrows = 0;
        if (isset($result[0]['numrows'])) {
            $numrows = (int)$result[0]['numrows'];
        }
        $exists = ($numrows > 0);

        return $exists;
    }

    /**
     * 複合主キーの問い合わせ条件を組み立てる
     * 
     * @return array 問い合わせ条件
     */
    private function builConditionsOfPrimaryKey() {
        foreach ($this->compositePrimaryKey as $field) {
            $$field = Set::extract("{$this->alias}.{$field}", $this->data);
        }
        $conditions = compact($this->compositePrimaryKey);

        return $conditions;
    }
 
    /**
     * モデルで定義したフィールドを対象に、フィールド名と値のペアを返却
     * 
     * @return array 更新系の命令を実行するためのデータセット
     */
    private function buildQueryToUpdateAllItems() {
        $dataSource = $this->getDataSource();

        $bufFields = array();
        $bufValues = array();
        foreach ($this->fieldsDefinition as $field => $columnType) {
            $bufFields[] = $field;
            $rawValue    = Set::extract("{$this->alias}.{$field}", $this->data);
            $bufValues[] = $dataSource->value($rawValue, $columnType, false);
        }
        $builtQuery = array($bufFields, $bufValues);

        return $builtQuery;
    }
}
