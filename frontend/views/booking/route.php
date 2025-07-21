<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $bus common\models\Bus */
/* @var $routes common\models\Route[] */

$this->title = 'Select Route';
$this->params['breadcrumbs'][] = ['label' => 'Select Bus', 'url' => ['bus']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container py-5">
    <div class="text-center mb-5">
        <span class="display-4 text-primary"><i class="bi bi-geo-alt"></i></span>
        <h1 class="fw-bold mb-2">Choose Your Route</h1>
        <p class="lead text-muted">Select a route for your journey.</p>
        <div class="mt-2">
            <span class="badge bg-primary">Bus: <?= Html::encode($bus->type) ?> (<?= Html::encode($bus->plate_number) ?>)</span>
        </div>
    </div>
    <div class="row justify-content-center">
        <?php foreach ($routes as $route): ?>
            <div class="col-md-6 mb-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title mb-2 text-primary"><i class="bi bi-signpost"></i> <?= Html::encode($route->origin) ?> → <?= Html::encode($route->destination) ?></h5>
                        <p class="mb-1">Price: <b><?= number_format($route->price) ?> TZS</b></p>
                        <?= Html::a('Select', ['seat', 'bus_id' => $bus->id, 'route_id' => $route->id], ['class' => 'btn btn-primary btn-lg w-100']) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div> 