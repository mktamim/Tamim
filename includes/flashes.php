<?php
$flashes = tamim_flashes();
foreach ($flashes as $flash):
?>
<div class="notice notice-<?php echo tamim_e($flash['type']); ?>" role="status"><?php echo tamim_e($flash['message']); ?></div>
<?php endforeach; ?>
