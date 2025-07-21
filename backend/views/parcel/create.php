<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Parcel */

$this->title = 'Create Parcel';
$this->params['breadcrumbs'][] = ['label' => 'Parcels', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="parcel-create">
    <h1><?= Html::encode($this->title) ?></h1>
    
    <!-- Debug info -->
    <div class="alert alert-info">
        <strong>Debug:</strong> Form should redirect to preview page after submission.
        <br>Check browser console for debug messages.
    </div>
    
    <?php if ($model->hasErrors()): ?>
        <div class="alert alert-danger">
            <?= Html::errorSummary($model) ?>
        </div>
    <?php endif; ?>
    
    <?php $form = ActiveForm::begin(['id' => 'parcel-create-form']); ?>
    
    <?= $form->field($model, 'user_id')->dropDownList(
        \yii\helpers\ArrayHelper::map(\common\models\User::find()->all(), 'id', 'username'),
        ['prompt' => 'Select user']
    ) ?>
    
    <?= $form->field($model, 'parcel_type')->dropDownList(
        \common\models\Parcel::getParcelTypeLabels(),
        ['prompt' => 'Select parcel type', 'id' => 'parcel-type-select']
    ) ?>
    
    <?= $form->field($model, 'parcel_category')->dropDownList(
        \common\models\Parcel::getParcelCategoryLabels(),
        ['prompt' => 'Select category']
    ) ?>
    
    <?= $form->field($model, 'weight')->textInput(['type' => 'number', 'step' => '0.1', 'id' => 'parcel-weight-input']) ?>
    
    <?= $form->field($model, 'route_id')->dropDownList(
        \yii\helpers\ArrayHelper::map(\common\models\Route::find()->where(['status' => 'active'])->all(), 'id', 'name'),
        ['prompt' => 'Select route']
    ) ?>
    
    <?= $form->field($model, 'departure_date')->input('date') ?>
    <?= $form->field($model, 'sender_name')->textInput() ?>
    <?= $form->field($model, 'sender_phone')->textInput() ?>
    <?= $form->field($model, 'sender_address')->textInput() ?>
    <?= $form->field($model, 'recipient_name')->textInput() ?>
    <?= $form->field($model, 'recipient_phone')->textInput() ?>
    <?= $form->field($model, 'recipient_address')->textInput() ?>
    <?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>
    
    <?= $form->field($model, 'price')->hiddenInput(['id' => 'parcel-price'])->label(false) ?>
    
    <div class="form-group mt-3">
        <?= Html::submitButton('Submit', [
            'class' => 'btn btn-success btn-lg',
            'id' => 'create-parcel-btn'
        ]) ?>
        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-secondary']) ?>
        
        <!-- Test button -->
        <?= Html::a('Test Redirect', ['test'], [
            'class' => 'btn btn-warning',
            'onclick' => 'console.log("Test redirect clicked");'
        ]) ?>
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
    const form = document.getElementById('parcel-create-form');
    const submitBtn = document.getElementById('create-parcel-btn');
    
    console.log('Form loaded:', form);
    
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
    
    // Handle form submission
    form.addEventListener('submit', function(e) {
        console.log('Form submitted!');
        
        // Validate required fields
        const requiredFields = [
            'Parcel[user_id]',
            'Parcel[parcel_type]',
            'Parcel[parcel_category]',
            'Parcel[weight]',
            'Parcel[route_id]',
            'Parcel[sender_name]',
            'Parcel[sender_phone]',
            'Parcel[recipient_name]',
            'Parcel[recipient_phone]'
        ];
        
        let isValid = true;
        requiredFields.forEach(fieldName => {
            const field = document.querySelector(`[name="${fieldName}"]`);
            if (!field || !field.value.trim()) {
                isValid = false;
                console.log('Missing required field:', fieldName);
                if (field) {
                    field.style.borderColor = 'red';
                }
            } else if (field) {
                field.style.borderColor = '';
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields (marked in red).');
            return false;
        }
        
        // Disable button to prevent double submission
        submitBtn.disabled = true;
        submitBtn.textContent = 'Creating Parcel...';
        
        console.log('Form is valid, submitting...');
        
        // Log form data for debugging
        const formData = new FormData(form);
        console.log('Form data:');
        for (let [key, value] of formData.entries()) {
            console.log(key + ': ' + value);
        }
    });
});
</script> 