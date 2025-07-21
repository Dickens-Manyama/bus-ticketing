<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Parcel */

$this->title = 'Book a Parcel';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="parcel-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php $form = ActiveForm::begin(['id' => 'parcel-create-form']); ?>
    <?= $form->field($model, 'parcel_type')->dropDownList(
        \common\models\Parcel::getParcelTypeLabels(),
        ['prompt' => 'Select parcel type', 'id' => 'parcel-type-select']
    ) ?>
    <?= $form->field($model, 'parcel_category')->dropDownList(
        \common\models\Parcel::getParcelCategoryLabels(),
        ['prompt' => 'Select category']
    ) ?>
    <?= $form->field($model, 'weight')->textInput(['type' => 'number', 'step' => '0.1', 'id' => 'parcel-weight-input']) ?>
    <?= $form->field($model, 'route_id')->textInput() ?>
    <?= $form->field($model, 'departure_date')->input('date') ?>
    <?= $form->field($model, 'sender_name')->textInput() ?>
    <?= $form->field($model, 'sender_phone')->textInput() ?>
    <?= $form->field($model, 'sender_address')->textInput() ?>
    <?= $form->field($model, 'recipient_name')->textInput() ?>
    <?= $form->field($model, 'recipient_phone')->textInput() ?>
    <?= $form->field($model, 'recipient_address')->textInput() ?>
    <?= $form->field($model, 'price')->hiddenInput(['id' => 'parcel-price'])->label(false) ?>
    <div class="form-group mt-3">
        <?= Html::submitButton('Submit', [
            'class' => 'btn btn-success btn-lg',
            'id' => 'create-parcel-btn'
        ]) ?>
        <?= Html::a('Cancel', ['create'], ['class' => 'btn btn-secondary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
    <div class="alert alert-info mt-3" id="price-info" style="display:none;"></div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const parcelTypeSelect = document.getElementById('parcel-type-select');
    const weightInput = document.getElementById('parcel-weight-input');
    const priceField = document.getElementById('parcel-price');
    const priceInfo = document.getElementById('price-info');
    function updatePrice() {
        const type = parcelTypeSelect.value;
        const weight = parseFloat(weightInput.value) || 0;
        const rates = {
            'small': 1000,
            'medium': 1500,
            'large': 2000,
            'extra_large': 2500
        };
        let price = 0;
        if (type && weight > 0) {
            price = rates[type] ? rates[type] * weight : 1000 * weight;
        }
        priceField.value = Math.round(price);
        if (type && weight > 0) {
            priceInfo.style.display = 'block';
            priceInfo.textContent = 'Calculated Price: ' + Math.round(price) + ' TZS';
        } else {
            priceInfo.style.display = 'none';
        }
    }
    parcelTypeSelect.addEventListener('change', updatePrice);
    weightInput.addEventListener('input', updatePrice);
    updatePrice();
});
</script> 