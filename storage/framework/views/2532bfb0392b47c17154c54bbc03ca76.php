<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Paralkes+'); ?> — Mitra Layanan Kesehatan Anda</title>

    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300..700&family=Bebas+Neue&display=swap" rel="stylesheet">

    
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>

    
    <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
    <?php echo $__env->yieldContent('content'); ?>

    <script src="<?php echo e(asset('js/theme.js')); ?>"></script>
    <script>lucide.createIcons();</script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH C:\laragon\www\paralkesplus\resources\views/layouts/app.blade.php ENDPATH**/ ?>