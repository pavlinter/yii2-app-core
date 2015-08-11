<?php

use kartik\widgets\Select2;
use pavlinter\buttons\InputButton;
use yii\helpers\Url;
use pavlinter\adm\Adm;

/* @var $this yii\web\View */
/* @var $model pavlinter\adm\models\User */
/* @var $form yii\widgets\ActiveForm */
/* @var $dynamicModel yii\base\DynamicModel */
/* @var $authItems array */

\app\assets_b\SumoselectAsset::register($this);

$authItems = Adm::getInstance()->manager->createAuthItemQuery('find')->where(['not in', 'name', $this->context->excludeRole])->orderBy(['created_at' => SORT_ASC])->all();
$authAssignments = Adm::getInstance()->manager->createAuthAssignmentQuery('find')->select("item_name")->where(['user_id' => $model->id])->indexBy('item_name')->asArray()->all();

$this->registerJs('
    $(".sumoselect-role").SumoSelect({
        placeholder: "' . Adm::t('sumoselect', 'Select ...', ['dot' => false]) . '",
        captionFormat:"' . Adm::t('sumoselect', '{0} Selected', ['dot' => false]) . '",
        selectAlltext: "' . Adm::t('sumoselect', 'Select All', ['dot' => false]) . '"
    });
');

$lis = [
    'AdmRoot' => 'Root',
    'AdmAdmin' => 'Admin',
    'Adm-User' => 'Users',
    'Adm-Language' => 'Languages',
    'Adm-FilesRoot' => 'Media Files (Root)',
    'Adm-FilesAdmin' => 'Media Files (Admin)',
    'Adm-TranslRoot' => 'Translations',
    'Pages' => 'Pages',
];
?>

<div class="user-form">

    <?php $form = Adm::begin('ActiveForm'); ?>

    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-6">
            <?= $form->field($model, 'username')->textInput(['maxlength' => 255]) ?>


        </div>
        <div class="col-xs-12 col-sm-6 col-md-6">
            <?= $form->field($dynamicModel, 'password')->passwordInput()->label(Yii::t('modelAdm/user', 'Password')) ?>

        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-6">
            <?= $form->field($model, 'email')->textInput(['maxlength' => 255]) ?>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-6">
            <?= $form->field($dynamicModel, 'password2')->passwordInput()->label(Yii::t('modelAdm/user', 'Confirm Password')) ?>
        </div>
    </div>



    <?php if (!Adm::getInstance()->user->can('Adm-UpdateOwnUser', $model)) {?>

        <div class="row">
            <div class="col-xs-12 col-sm-4 col-md-4">
                <div class="form-group">
                    <label for="sumoselect-role" class="control-label"><?= Yii::t('modelAdm/user', 'Assignment Role') ?></label>
                    <select class="sumoselect-role SelectClass" name="roles[]" id="sumoselect-role" multiple="multiple">
                        <option class="group-head" disabled><?= Adm::t('sumoselect', 'Roles', ['dot' => false]) ?></option>
                        <?php foreach ($authItems as $authItem) {?>
                            <?php if ($authItem->type == 1) {?>
                                <option class="group-item" <?= isset($authAssignments[$authItem->name]) ? 'selected' : ''; ?> value="<?= $authItem->name ?>"><?= Adm::t('sumoselect/items', $authItem->name, ['dot' => false]) ?></option>
                            <?php }?>
                        <?php }?>
                        <option class="group-head" disabled><?= Adm::t('sumoselect', 'Permissions', ['dot' => false]) ?></option>
                        <?php foreach ($authItems as $authItem) {?>
                            <?php if ($authItem->type == 2) {?>
                                <option class="group-item" <?= isset($authAssignments[$authItem->name]) ? 'selected' : ''; ?> value="<?= $authItem->name ?>"><?= Adm::t('sumoselect/items', $authItem->name, ['dot' => false]) ?></option>
                            <?php }?>
                        <?php }?>
                    </select>
                    <?= Adm::t('sumoselect', 'Roles', ['dot' => '.']) ?>
                    <?= Adm::t('sumoselect', 'Permissions', ['dot' => '.']) ?>
                </div>
            </div>
            <div class="col-xs-12 col-sm-4 col-md-4">
                <?php
                echo $form->field($model, 'status')->widget(Select2::classname(), [
                    'data' => $model::status(),
                    'options' => ['placeholder' => Adm::t('','Select ...', ['dot' => false])],
                    'pluginOptions' => [

                    ],
                ]);
                ?>
            </div>
        </div>

    <?php }?>

    <div class="form-group">

        <?= InputButton::widget([
            'label' => $model->isNewRecord ? Adm::t('', 'Create', ['dot' => false]) : Adm::t('', 'Update', ['dot' => false]),
            'options' => ['class' => 'btn btn-primary'],
            'input' => 'adm-redirect',
            'name' => 'redirect',
            'formSelector' => $form,
        ]);?>

        <?php if (!Adm::getInstance()->user->can('Adm-UpdateOwnUser', $model)) {?>

            <?php if ($model->isNewRecord) {?>
                <?= InputButton::widget([
                    'label' => Adm::t('', 'Create and insert new', ['dot' => false]),
                    'options' => ['class' => 'btn btn-primary'],
                    'input' => 'adm-redirect',
                    'name' => 'redirect',
                    'value' => Url::to(['create']),
                    'formSelector' => $form, //form object or form selector
                ]);?>
            <?php }?>


            <?= InputButton::widget([
                'label' => $model->isNewRecord ? Adm::t('', 'Create and list', ['dot' => false]) : Adm::t('', 'Update and list', ['dot' => false]),
                'options' => ['class' => 'btn btn-primary'],
                'input' => 'adm-redirect',
                'name' => 'redirect',
                'value' => Url::to(['index']),
                'formSelector' => $form, //form object or form selector
            ]);?>

            <?= InputButton::widget([
                'label' => $model->isNewRecord ? Adm::t('', 'Create and viewing', ['dot' => false]) : Adm::t('', 'Update and viewing', ['dot' => false]),
                'options' => ['class' => 'btn btn-primary'],
                'input' => 'adm-redirect',
                'name' => 'redirect',
                'value' => Url::to(['view', 'id' => '{id}']),
                'formSelector' => $form, //form object or form selector
            ]);?>

        <?php }?>
    </div>

    <?php Adm::end('ActiveForm'); ?>

</div>
