<?php

/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = 'About';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        Welcome to <b>Dickens-OnlineTicketing</b>!<br>
        This platform allows you to book bus tickets online, manage your bookings, and much more.<br>
    </p>
    <b>Features:</b>
    <ul>
        <li>Real-time seat selection and booking</li>
        <li>Secure online payments</li>
        <li>Admin and user dashboards</li>
        <li>Messaging and notifications</li>
        <li>Receipts with QR codes</li>
        <li>Modern, mobile-friendly design</li>
    </ul>
    <p>
        For support or inquiries, contact us at <a href="mailto:dickensmanyama8@gmail.com">dickensmanyama8@gmail.com</a> or call +255679165468.
    </p>
</div>
