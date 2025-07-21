<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap5\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $statusCounts array */

$this->title = 'User Management';
$this->params['breadcrumbs'][] = $this->title;

// Register Bootstrap Icons
$this->registerCssFile('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css');
?>

<div class="user-index">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-people"></i> <?= Html::encode($this->title) ?></h1>
        <div>
            <?= Html::a('<i class="bi bi-plus-circle"></i> Create User', ['create'], ['class' => 'btn btn-success']) ?>
            <?php if (Yii::$app->user->identity->isManager()): ?>
                <?= Html::a('<i class="bi bi-person-lines-fill"></i> Staff Activity', ['staff-activity'], ['class' => 'btn btn-info ms-2']) ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Status Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-primary">
                <div class="card-body text-center">
                    <i class="bi bi-people display-6"></i>
                    <h5 class="card-title mt-2">Total Users</h5>
                    <p class="card-text display-6 fw-bold mb-0"><?= $statusCounts['total'] ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-success">
                <div class="card-body text-center">
                    <i class="bi bi-check-circle display-6"></i>
                    <h5 class="card-title mt-2">Active</h5>
                    <p class="card-text display-6 fw-bold mb-0"><?= $statusCounts['active'] ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-warning">
                <div class="card-body text-center">
                    <i class="bi bi-exclamation-circle display-6"></i>
                    <h5 class="card-title mt-2">Inactive</h5>
                    <p class="card-text display-6 fw-bold mb-0"><?= $statusCounts['inactive'] ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-danger">
                <div class="card-body text-center">
                    <i class="bi bi-trash display-6"></i>
                    <h5 class="card-title mt-2">Deleted</h5>
                    <p class="card-text display-6 fw-bold mb-0"><?= $statusCounts['deleted'] ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="btn-group" role="group">
                        <?= Html::a('<i class="bi bi-download"></i> Export CSV', array_merge(['export-csv'], Yii::$app->request->queryParams), ['class' => 'btn btn-outline-secondary']) ?>
                        <?= Html::a('<i class="bi bi-file-earmark-excel"></i> Export Excel', array_merge(['export-excel'], Yii::$app->request->queryParams), ['class' => 'btn btn-outline-success']) ?>
                        <?= Html::a('<i class="bi bi-printer"></i> Print Report', ['#'], ['class' => 'btn btn-outline-primary', 'onclick' => 'window.print(); return false;']) ?>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#bulkStatusModal">
                        <i class="bi bi-gear"></i> Bulk Status Update
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php Pjax::begin(); ?>
    
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-search"></i> Search & Filter Users</h5>
        </div>
        <div class="card-body">
            <?= $this->render('_search', ['model' => $searchModel]); ?>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-list-ul"></i> User List</h5>
        </div>
        <div class="card-body p-0">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'tableOptions' => ['class' => 'table table-striped table-hover mb-0'],
                'headerRowOptions' => ['class' => 'table-light'],
                'columns' => [
                    [
                        'class' => 'yii\grid\CheckboxColumn',
                        'header' => '<input type="checkbox" id="select-all">',
                    ],
                    [
                        'class' => 'yii\grid\SerialColumn',
                        'header' => '#',
                    ],
                    [
                        'attribute' => 'id',
                        'headerOptions' => ['style' => 'width: 60px;'],
                    ],
                    [
                        'attribute' => 'username',
                        'format' => 'raw',
                        'value' => function($model) {
                            return Html::encode($model->username);
                        }
                    ],
                    'email:email',
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'filter' => [
                            \common\models\User::STATUS_ACTIVE => 'Active',
                            \common\models\User::STATUS_INACTIVE => 'Inactive', 
                            \common\models\User::STATUS_DELETED => 'Deleted'
                        ],
                        'value' => function($model) {
                            $statusLabels = [
                                \common\models\User::STATUS_ACTIVE => ['Active', 'success'],
                                \common\models\User::STATUS_INACTIVE => ['Inactive', 'warning'],
                                \common\models\User::STATUS_DELETED => ['Deleted', 'danger']
                            ];
                            $label = $statusLabels[$model->status] ?? ['Unknown', 'secondary'];
                            return Html::tag('span', $label[0], ['class' => "badge bg-{$label[1]}"]);
                        }
                    ],
                    [
                        'attribute' => 'role',
                        'format' => 'raw',
                        'filter' => [
                            'superadmin' => 'Super Admin',
                            'admin' => 'Admin', 
                            'manager' => 'Manager',
                            'staff' => 'Staff',
                            'user' => 'User'
                        ],
                        'value' => function($model) {
                            $roleColors = [
                                'superadmin' => 'danger',
                                'admin' => 'primary',
                                'manager' => 'info',
                                'staff' => 'warning',
                                'user' => 'secondary'
                            ];
                            $color = $roleColors[$model->role] ?? 'secondary';
                            $currentUser = Yii::$app->user->identity;
                            if (in_array($model->role, ['superadmin', 'admin']) && !in_array($currentUser->role, ['superadmin', 'admin'])) {
                                $label = $model->role === 'superadmin' ? 'Super Admin' : 'Admin';
                                $icon = $model->role === 'superadmin' ? 'bi-shield-lock-fill text-danger' : 'bi-person-badge-fill text-primary';
                                return Html::tag('span', $label, [
                                    'class' => "badge bg-{$color}",
                                    'title' => "$label (protected)",
                                    'data-bs-toggle' => 'tooltip',
                                    'data-bs-placement' => 'top',
                                    'style' => 'cursor: help;'
                                ]) . " <i class=\"bi {$icon}\" title=\"$label (protected)\"></i>";
                            }
                            if ($model->role === 'superadmin' && !$currentUser->isSuperAdmin()) {
                                return Html::tag('span', 'Super Admin', [
                                    'class' => "badge bg-{$color}",
                                    'title' => 'Super Admin (protected)',
                                    'data-bs-toggle' => 'tooltip',
                                    'data-bs-placement' => 'top',
                                    'style' => 'cursor: help;'
                                ]) . ' <i class="bi bi-shield-lock-fill text-danger" title="Super Admin (protected)"></i>';
                            }
                            if ($model->role === 'admin' && $currentUser->role !== 'admin' && $currentUser->role !== 'superadmin') {
                                return Html::tag('span', 'Admin', [
                                    'class' => "badge bg-{$color}",
                                    'title' => 'Admin (protected)',
                                    'data-bs-toggle' => 'tooltip',
                                    'data-bs-placement' => 'top',
                                    'style' => 'cursor: help;'
                                ]) . ' <i class="bi bi-person-badge-fill text-primary" title="Admin (protected)"></i>';
                            }
                            return Html::tag('span', ucfirst($model->role), ['class' => "badge bg-{$color}"]);
                        }
                    ],
                    [
                        'attribute' => 'created_at',
                        'format' => 'datetime',
                        'headerOptions' => ['style' => 'width: 150px;'],
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header' => 'Actions',
                        'headerOptions' => ['style' => 'width: 120px;'],
                        'template' => '{view} {update} {delete}',
                        'buttons' => [
                            'view' => function ($url, $model) {
                                return Html::a('<i class="bi bi-eye"></i>', $url, [
                                    'class' => 'btn btn-sm btn-outline-primary',
                                    'title' => 'View',
                                ]);
                            },
                            'update' => function ($url, $model) {
                                $currentUser = Yii::$app->user->identity;
                                if ((in_array($model->role, ['superadmin', 'admin']) && !in_array($currentUser->role, ['superadmin', 'admin']))) {
                                    return '';
                                }
                                if ($model->role === 'superadmin' && !$currentUser->isSuperAdmin()) {
                                    return '';
                                }
                                if ($model->role === 'admin' && $currentUser->role !== 'admin' && $currentUser->role !== 'superadmin') {
                                    return '';
                                }
                                return Html::a('<i class="bi bi-pencil"></i>', $url, [
                                    'class' => 'btn btn-sm btn-outline-warning',
                                    'title' => 'Update',
                                ]);
                            },
                            'delete' => function ($url, $model) {
                                $currentUser = Yii::$app->user->identity;
                                if ((in_array($model->role, ['superadmin', 'admin']) && !in_array($currentUser->role, ['superadmin', 'admin']))) {
                                    return '';
                                }
                                if ($model->role === 'superadmin' && !$currentUser->isSuperAdmin()) {
                                    return '';
                                }
                                if ($model->role === 'admin' && $currentUser->role !== 'admin' && $currentUser->role !== 'superadmin') {
                                    return '';
                                }
                                return Html::a('<i class="bi bi-trash"></i>', $url, [
                                    'class' => 'btn btn-sm btn-outline-danger',
                                    'title' => 'Delete',
                                    'data-confirm' => 'Are you sure you want to delete this user?',
                                    'data-method' => 'post',
                                ]);
                            },
                        ],
                    ],
                ],
            ]); ?>
        </div>
    </div>

    <?php Pjax::end(); ?>
</div>

<!-- Bulk Status Update Modal -->
<div class="modal fade" id="bulkStatusModal" tabindex="-1" aria-labelledby="bulkStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkStatusModalLabel">
                    <i class="bi bi-gear"></i> Bulk Status Update
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php $form = ActiveForm::begin(['action' => ['bulk-status-update'], 'method' => 'post']); ?>
            <div class="modal-body">
                <p>Select users and choose a new status:</p>
                <div class="mb-3">
                    <label class="form-label">New Status:</label>
                    <select name="status" class="form-select" required>
                        <option value="">Select Status</option>
                        <option value="<?= \common\models\User::STATUS_ACTIVE ?>">Active</option>
                        <option value="<?= \common\models\User::STATUS_INACTIVE ?>">Inactive</option>
                        <option value="<?= \common\models\User::STATUS_DELETED ?>">Deleted</option>
                    </select>
                </div>
                <input type="hidden" name="userIds" id="selectedUserIds" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Status</button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle select all checkbox
    document.getElementById('select-all').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('input[name="selection[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelectedUsers();
    });

    // Handle individual checkboxes
    document.addEventListener('change', function(e) {
        if (e.target.name === 'selection[]') {
            updateSelectedUsers();
        }
    });

    function updateSelectedUsers() {
        const checkboxes = document.querySelectorAll('input[name="selection[]"]:checked');
        const userIds = Array.from(checkboxes).map(cb => cb.value);
        document.getElementById('selectedUserIds').value = userIds.join(',');
    }
});
</script> 