<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = 'Real-Time Seat Monitoring';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="bi bi-display text-primary"></i> Seat Monitoring
                    </h1>
                    <p class="text-muted mb-0">Real-time bus seat management and journey control</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?= Url::to(['real-time-dashboard']) ?>" class="btn btn-outline-primary">
                        <i class="bi bi-speedometer2"></i> Real-time Dashboard
                    </a>
                    <button class="btn btn-outline-secondary" onclick="location.reload()">
                        <i class="bi bi-arrow-clockwise"></i> Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Test Button -->
    <div class="row mb-3">
        <div class="col-12">
            <button class="btn btn-info" onclick="testButton()">Test Button - Click Me!</button>
        </div>
    </div>
    
    <!-- Report Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">
                        <i class="bi bi-display"></i> Real-Time Seat Monitoring Dashboard
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <a href="<?= Url::to(['real-time-dashboard']) ?>" class="btn btn-success">
                                <i class="bi bi-speedometer2"></i> Live Dashboard
                            </a>
                            <button class="btn btn-info" onclick="location.reload()">
                                <i class="bi bi-arrow-clockwise"></i> Refresh All
                            </button>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="badge bg-success fs-6">
                                <i class="bi bi-wifi"></i> Real-time Updates Active
                            </div>
                        </div>
                    </div>

                    <?php Pjax::begin(['id' => 'buses-pjax', 'timeout' => 10000]); ?>
                    
                    <?php
                    ?>
                    
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => null,
                        'columns' => [
                            [
                                'class' => 'yii\grid\SerialColumn',
                                'header' => '#',
                            ],
                            [
                                'attribute' => 'plate_number',
                                'label' => 'Bus Plate',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return Html::tag('strong', $model->plate_number);
                                },
                            ],
                            [
                                'attribute' => 'class',
                                'label' => 'Class',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    $badgeClass = $model->class === 'Luxury' ? 'bg-warning' : 'bg-info';
                                    return Html::tag('span', $model->class, ['class' => "badge {$badgeClass}"]);
                                },
                            ],
                            [
                                'label' => 'Route',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    // Get the most common route from bookings for this bus
                                    $bookings = $model->bookings;
                                    if (!empty($bookings)) {
                                        // Group bookings by route_id and count them
                                        $routeCounts = [];
                                        foreach ($bookings as $booking) {
                                            if ($booking->route) {
                                                $routeId = $booking->route_id;
                                                if (!isset($routeCounts[$routeId])) {
                                                    $routeCounts[$routeId] = [
                                                        'count' => 0,
                                                        'route' => $booking->route
                                                    ];
                                                }
                                                $routeCounts[$routeId]['count']++;
                                            }
                                        }
                                        
                                        // Find the route with most bookings
                                        if (!empty($routeCounts)) {
                                            $mostBookedRoute = null;
                                            $maxCount = 0;
                                            foreach ($routeCounts as $routeData) {
                                                if ($routeData['count'] > $maxCount) {
                                                    $maxCount = $routeData['count'];
                                                    $mostBookedRoute = $routeData['route'];
                                                }
                                            }
                                            
                                            if ($mostBookedRoute) {
                                                return Html::tag('div', 
                                                    $mostBookedRoute->origin . ' → ' . $mostBookedRoute->destination,
                                                    ['class' => 'fw-bold']
                                                );
                                            }
                                        }
                                    }
                                    
                                    // Fallback: check if bus has assigned route
                                    if (isset($model->route_id) && $model->route_id) {
                                        $route = \common\models\Route::findOne($model->route_id);
                                        if ($route) {
                                            return Html::tag('div', 
                                                $route->origin . ' → ' . $route->destination,
                                                ['class' => 'fw-bold']
                                            );
                                        }
                                    }
                                    
                                    return Html::tag('span', 'No Bookings', ['class' => 'text-muted']);
                                },
                            ],
                            [
                                'label' => 'Seat Occupancy',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    $bookedSeats = count($model->bookings);
                                    $totalSeats = $model->seat_count;
                                    $availableSeats = $totalSeats - $bookedSeats;
                                    $occupancyRate = $totalSeats > 0 ? round(($bookedSeats / $totalSeats) * 100, 1) : 0;
                                    
                                    $progressClass = $occupancyRate >= 90 ? 'bg-danger' : 
                                                   ($occupancyRate >= 70 ? 'bg-warning' : 'bg-success');
                                    
                                    $html = Html::tag('div', 
                                        Html::tag('small', "Booked: {$bookedSeats}") . 
                                        Html::tag('small', "Available: {$availableSeats}", ['class' => 'ms-2']), 
                                        ['class' => 'mb-1']
                                    );
                                    
                                    $html .= Html::tag('div', 
                                        Html::tag('div', '', [
                                            'class' => "progress-bar {$progressClass}",
                                            'style' => "width: {$occupancyRate}%",
                                            'title' => "{$occupancyRate}% occupied"
                                        ]), 
                                        ['class' => 'progress', 'style' => 'height: 8px;']
                                    );
                                    
                                    return $html;
                                },
                            ],
                            [
                                'label' => 'Status',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    // Get the most common route from bookings for this bus
                                    $bookings = $model->bookings;
                                    if (!empty($bookings)) {
                                        // Group bookings by route_id and count them
                                        $routeCounts = [];
                                        foreach ($bookings as $booking) {
                                            if ($booking->route) {
                                                $routeId = $booking->route_id;
                                                if (!isset($routeCounts[$routeId])) {
                                                    $routeCounts[$routeId] = [
                                                        'count' => 0,
                                                        'route' => $booking->route
                                                    ];
                                                }
                                                $routeCounts[$routeId]['count']++;
                                            }
                                        }
                                        
                                        // Find the route with most bookings
                                        if (!empty($routeCounts)) {
                                            $mostBookedRoute = null;
                                            $maxCount = 0;
                                            foreach ($routeCounts as $routeData) {
                                                if ($routeData['count'] > $maxCount) {
                                                    $maxCount = $routeData['count'];
                                                    $mostBookedRoute = $routeData['route'];
                                                }
                                            }
                                            
                                            if ($mostBookedRoute) {
                                                $status = $mostBookedRoute->status;
                                                $statusText = '';
                                                $badgeClass = '';
                                                
                                                switch ($status) {
                                                    case 'pending':
                                                        $statusText = 'Active';
                                                        $badgeClass = 'bg-success';
                                                        break;
                                                    case 'in_progress':
                                                        $statusText = 'On Journey';
                                                        $badgeClass = 'bg-warning';
                                                        break;
                                                    case 'completed':
                                                        $statusText = 'Final Destination';
                                                        $badgeClass = 'bg-danger';
                                                        break;
                                                    case 'cancelled':
                                                        $statusText = 'Cancelled';
                                                        $badgeClass = 'bg-secondary';
                                                        break;
                                                    default:
                                                        $statusText = 'Unknown';
                                                        $badgeClass = 'bg-secondary';
                                                }
                                                
                                                return Html::tag('span', $statusText, ['class' => "badge {$badgeClass}"]);
                                            }
                                        }
                                    }
                                    
                                    // Fallback: check if bus has assigned route
                                    if (isset($model->route_id) && $model->route_id) {
                                        $route = \common\models\Route::findOne($model->route_id);
                                        if ($route) {
                                            $status = $route->status;
                                            $statusText = '';
                                            $badgeClass = '';
                                            
                                            switch ($status) {
                                                case 'pending':
                                                    $statusText = 'Active';
                                                    $badgeClass = 'bg-success';
                                                    break;
                                                case 'in_progress':
                                                    $statusText = 'On Journey';
                                                    $badgeClass = 'bg-warning';
                                                    break;
                                                case 'completed':
                                                    $statusText = 'Final Destination';
                                                    $badgeClass = 'bg-danger';
                                                    break;
                                                case 'cancelled':
                                                    $statusText = 'Cancelled';
                                                    $badgeClass = 'bg-secondary';
                                                    break;
                                                default:
                                                    $statusText = 'Unknown';
                                                    $badgeClass = 'bg-secondary';
                                            }
                                            
                                            return Html::tag('span', $statusText, ['class' => "badge {$badgeClass}"]);
                                        }
                                    }
                                    
                                    return Html::tag('span', 'No Route', ['class' => 'badge bg-secondary']);
                                },
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => 'Actions',
                                'template' => '{view} {start} {finish} {journey}',
                                'buttons' => [
                                    'view' => function ($url, $model) {
                                        return Html::a(
                                            '<i class="bi bi-eye"></i>',
                                            ['bus-seats', 'bus_id' => $model->id],
                                            [
                                                'class' => 'btn btn-sm btn-outline-primary',
                                                'title' => 'View Seat Map',
                                                'data-pjax' => '0'
                                            ]
                                        );
                                    },
                                    'start' => function ($url, $model) {
                                        // Check if bus has assigned route first
                                        if (isset($model->route_id) && $model->route_id) {
                                            $route = \common\models\Route::findOne($model->route_id);
                                            if ($route && $route->status === 'in_progress') {
                                                $disabled = 'disabled';
                                            } else {
                                                $disabled = '';
                                            }
                                        } else {
                                            // If no assigned route, check bookings for route
                                            $bookings = $model->bookings;
                                            if (!empty($bookings)) {
                                                // Group bookings by route_id and count them
                                                $routeCounts = [];
                                                foreach ($bookings as $booking) {
                                                    if ($booking->route) {
                                                        $routeId = $booking->route_id;
                                                        if (!isset($routeCounts[$routeId])) {
                                                            $routeCounts[$routeId] = [
                                                                'count' => 0,
                                                                'route' => $booking->route
                                                            ];
                                                        }
                                                        $routeCounts[$routeId]['count']++;
                                                    }
                                                }
                                                
                                                // Find the route with most bookings
                                                if (!empty($routeCounts)) {
                                                    $mostBookedRoute = null;
                                                    $maxCount = 0;
                                                    foreach ($routeCounts as $routeData) {
                                                        if ($routeData['count'] > $maxCount) {
                                                            $maxCount = $routeData['count'];
                                                            $mostBookedRoute = $routeData['route'];
                                                        }
                                                    }
                                                    
                                                    if ($mostBookedRoute && $mostBookedRoute->status === 'in_progress') {
                                                        $disabled = 'disabled';
                                                    } else {
                                                        $disabled = '';
                                                    }
                                                } else {
                                                    $disabled = '';
                                                }
                                            } else {
                                                // Empty bus - allow route management
                                                $disabled = '';
                                            }
                                        }
                                        
                                        return Html::button(
                                            '<i class="bi bi-play-circle"></i>',
                                            [
                                                'class' => "btn btn-sm btn-success {$disabled}",
                                                'title' => 'Start Route',
                                                'onclick' => "startRoute({$model->id})",
                                                'disabled' => $disabled
                                            ]
                                        );
                                    },
                                    'finish' => function ($url, $model) {
                                        // Check if bus has assigned route first
                                        if (isset($model->route_id) && $model->route_id) {
                                            $route = \common\models\Route::findOne($model->route_id);
                                            if ($route && $route->status === 'completed') {
                                                $disabled = 'disabled';
                                            } else {
                                                $disabled = '';
                                            }
                                        } else {
                                            // If no assigned route, check bookings for route
                                            $bookings = $model->bookings;
                                            if (!empty($bookings)) {
                                                // Group bookings by route_id and count them
                                                $routeCounts = [];
                                                foreach ($bookings as $booking) {
                                                    if ($booking->route) {
                                                        $routeId = $booking->route_id;
                                                        if (!isset($routeCounts[$routeId])) {
                                                            $routeCounts[$routeId] = [
                                                                'count' => 0,
                                                                'route' => $booking->route
                                                            ];
                                                        }
                                                        $routeCounts[$routeId]['count']++;
                                                    }
                                                }
                                                
                                                // Find the route with most bookings
                                                if (!empty($routeCounts)) {
                                                    $mostBookedRoute = null;
                                                    $maxCount = 0;
                                                    foreach ($routeCounts as $routeData) {
                                                        if ($routeData['count'] > $maxCount) {
                                                            $maxCount = $routeData['count'];
                                                            $mostBookedRoute = $routeData['route'];
                                                        }
                                                    }
                                                    
                                                    if ($mostBookedRoute && $mostBookedRoute->status === 'completed') {
                                                        $disabled = 'disabled';
                                                    } else {
                                                        $disabled = '';
                                                    }
                                                } else {
                                                    $disabled = '';
                                                }
                                            } else {
                                                // Empty bus - allow route management
                                                $disabled = '';
                                            }
                                        }
                                        
                                        return Html::button(
                                            '<i class="bi bi-stop-circle"></i>',
                                            [
                                                'class' => "btn btn-sm btn-danger {$disabled}",
                                                'title' => 'Finish Route',
                                                'onclick' => "finishRoute({$model->id})",
                                                'disabled' => $disabled
                                            ]
                                        );
                                    },
                                    'journey' => function ($url, $model) {
                                        $startButton = Html::button(
                                            '<i class="bi bi-play-circle"></i> Start Journey',
                                            [
                                                'class' => 'btn btn-sm btn-success me-1',
                                                'title' => 'Start Journey',
                                                'onclick' => "startJourney({$model->id})"
                                            ]
                                        );
                                        
                                        $finishButton = Html::button(
                                            '<i class="bi bi-stop-circle"></i> Finish Journey',
                                            [
                                                'class' => 'btn btn-sm btn-warning me-1',
                                                'title' => 'Finish Journey',
                                                'onclick' => "finishJourney({$model->id})"
                                            ]
                                        );
                                        
                                        $newJourneyButton = Html::button(
                                            '<i class="bi bi-arrow-clockwise"></i> Start New Journey',
                                            [
                                                'class' => 'btn btn-sm btn-primary',
                                                'title' => 'Start New Journey',
                                                'onclick' => "startNewJourney({$model->id})"
                                            ]
                                        );
                                        
                                        return $startButton . $finishButton . $newJourneyButton;
                                    },
                                ],
                            ],
                        ],
                        'tableOptions' => ['class' => 'table table-striped table-hover'],
                        'summary' => 'Showing <b>{begin}-{end}</b> of <b>{totalCount}</b> buses.',
                    ]); ?>
                    
                    <?php Pjax::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.progress {
    background-color: #e9ecef;
    border-radius: 4px;
}
.progress-bar {
    transition: width 0.6s ease;
}
</style>

<script>
// Test if jQuery is loaded
$(document).ready(function() {
    console.log('jQuery is loaded and working!');
    alert('jQuery is loaded and working!');
});

function testButton() {
    alert('Test button clicked! onclick events are working!');
}

// Auto-refresh every 30 seconds
setInterval(function() {
    $.pjax.reload({container: '#buses-pjax'});
}, 30000);

function refreshAllData() {
    $.pjax.reload({container: '#buses-pjax'});
}

function startJourney(busId) {
    alert('startJourney called for busId: ' + busId);
    console.log('startJourney called with busId:', busId);
    if (confirm('Are you sure you want to start the journey for this bus?')) {
        $.ajax({
            url: '<?= Url::to(['start-journey']) ?>',
            type: 'POST',
            data: {
                bus_id: busId,
                <?= Yii::$app->request->csrfParam ?>: '<?= Yii::$app->request->csrfToken ?>'
            },
            dataType: 'json',
            success: function(response) {
                console.log('startJourney success response:', response);
                if (response.success) {
                    alert('Journey started! Bus is now on the road.');
                    refreshAllData();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('startJourney AJAX Error:', xhr.responseText);
                console.error('Status:', status);
                console.error('Error:', error);
                alert('Failed to start journey. Please try again.');
            }
        });
    }
}

function finishJourney(busId) {
    alert('finishJourney called for busId: ' + busId);
    console.log('finishJourney called with busId:', busId);
    if (confirm('Are you sure you want to finish the current journey for this bus?')) {
        $.ajax({
            url: '<?= Url::to(['finish-journey']) ?>',
            type: 'POST',
            data: {
                bus_id: busId,
                <?= Yii::$app->request->csrfParam ?>: '<?= Yii::$app->request->csrfToken ?>'
            },
            dataType: 'json',
            success: function(response) {
                console.log('finishJourney success response:', response);
                if (response.success) {
                    alert('Journey finished! All seats are now available.');
                    refreshAllData();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('finishJourney AJAX Error:', xhr.responseText);
                console.error('Status:', status);
                console.error('Error:', error);
                alert('Failed to finish journey. Please try again.');
            }
        });
    }
}

function startNewJourney(busId) {
    alert('startNewJourney called for busId: ' + busId);
    console.log('startNewJourney called with busId:', busId);
    if (confirm('Are you sure you want to start a new journey? This will reset the bus for new bookings.')) {
        $.ajax({
            url: '<?= Url::to(['start-new-journey']) ?>',
            type: 'POST',
            data: {
                bus_id: busId,
                <?= Yii::$app->request->csrfParam ?>: '<?= Yii::$app->request->csrfToken ?>'
            },
            dataType: 'json',
            success: function(response) {
                console.log('startNewJourney success response:', response);
                if (response.success) {
                    alert('New journey started! Bus is ready for bookings.');
                    refreshAllData();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('startNewJourney AJAX Error:', xhr.responseText);
                console.error('Status:', status);
                console.error('Error:', error);
                alert('Failed to start new journey. Please try again.');
            }
        });
    }
}
</script> 