<?php


namespace app\widgets;

use app\helpers\Url;
use kartik\grid\GridView;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Response;

/**
 *  [
 *      'class' => 'app\widgets\BooleanColumn',
 *      'attribute' => 'active',
 *      'tableName' => $searchModel::tableName(),
 *  ],
 */
class BooleanColumn extends \kartik\grid\BooleanColumn
{
    public $tableName;

    public $tableColumn;

    public $db = 'db';

    public $buttonOptions = [
        'class' => 'btn btn-default',
    ];

    public $buttonClass = 'ajax-btn';

    public $loading = '<i class=\"fa fa-spinner fa-spin\"></i>';

    public $filterType = GridView::FILTER_SELECT2;
    public $vAlign = 'middle';
    public $width = '50px';

    public $filterWidgetOptions = [
        'options' => ['placeholder' => 'Select ...'],
        'pluginOptions' => [
            'width' => '100px',
            'allowClear' => true,
        ],
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (empty($this->tableName)) {
            throw new InvalidConfigException('The "tableName" property must be set.');
        }

        $view = Yii::$app->getView();
        Html::addCssClass($this->buttonOptions, $this->buttonClass);

        if (!$this->tableColumn) {
            $this->tableColumn = $this->attribute;
        }

        $id = Yii::$app->request->post('id');
        $tableColumn = Yii::$app->request->post('tableColumn');
        if (Yii::$app->request->isPost && $tableColumn === $this->tableColumn) {
            /* @var \yii\db\Connection $db */
            $db = Yii::$app->get($this->db);

            $json['r'] = false;
            $query = new Query();
            $row = $query->from($this->tableName)
                ->select(['a' => $this->tableColumn])
                ->where(['id' => $id])
                ->limit(1)->one($db);

            if ($row) {
                if ($row['a']) {
                    $active = false;
                    $result = $this->falseIcon;
                } else {
                    $active = true;
                    $result = $this->trueIcon;
                }

                $db->createCommand()->update($this->tableName, [$this->tableColumn => $active], 'id=:id', [':id' => $id])->execute();
                $json['r'] = true;
                $json['res'] = $result;
            }
            $response = Yii::$app->getResponse();
            $response->clearOutputBuffers();
            $response->setStatusCode(200);
            $response->format = Response::FORMAT_JSON;
            $response->data = $json;
            $response->send();
            Yii::$app->end();
        }


        $view->registerJs('
            $(document).on("click", ".' . $this->buttonClass . '", function(){
                var $btn = $(this);

                $btn.html("' . $this->loading . '");
                $btn.attr("disabled", true);

                $.ajax({
                    url: "' . Url::current() . '",
                    type: "POST",
                    dataType: "json",
                    data: {id: $btn.attr("data-id"), tableColumn: $btn.attr("data-column")},
                }).done(function(d){
                    if(d.r){
                        $btn.html(d.res);
                    }
                }).always(function(jqXHR, textStatus){
                    $btn.attr("disabled", false);
                });

            });
        ');

    }

    public function renderButton($result, $model, $key, $index)
    {
        $options = $this->buttonOptions;
        $options['data-id'] = $key;
        $options['data-column'] = $this->tableColumn;
        Html::addCssClass($options, $this->buttonClass . '-' . $key);
        return Html::submitButton($result, $options);
    }

    /**
     * @inheritdoc
     */
    public function getDataCellValue($model, $key, $index)
    {
        $value = null;
        if ($this->value !== null) {
            if (is_string($this->value)) {
                $value = ArrayHelper::getValue($model, $this->value);
            } else {
                $value = call_user_func($this->value, $model, $key, $index, $this);
            }
        } elseif ($this->attribute !== null) {
            $value = ArrayHelper::getValue($model, $this->attribute);
        }

        if ($value !== null) {
            $result = $value ? $this->trueIcon : $this->falseIcon ;
        } else {
            $result = $this->showNullAsFalse ? $this->falseIcon : $value;
        }
        return $this->renderButton($result, $model, $key, $index);
    }


}